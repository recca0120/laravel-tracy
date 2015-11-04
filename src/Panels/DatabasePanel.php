<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Events\Dispatcher;
use PDO;

class DatabasePanel extends AbstractPanel
{
    public $attributes = [
        'count' => 0,
        'totalTime' => 0,
        'queries' => [],
    ];

    public function subscribe(Dispatcher $events)
    {
        $events->listen('illuminate.query', function ($sql, $bindings, $time, $name) {
            $db = $this->app['db'];
            $connection = $db->connection($name);
            $pdo = $connection->getPdo();
            $this->onQuery($sql, $bindings, $time, $name, $db, $connection, $pdo);
        });
    }

    public function onQuery($sql, $bindings, $time, $name, $db, $connection, $pdo)
    {
        $runnableSql = $this->createRunnableSql($sql, $bindings);
        $dumpSql = $this->dumpSql($runnableSql);
        $explain = [];
        if ($connection->getDriverName() === 'mysql') {
            $explain = $this->getExplain($sql, $bindings, $pdo);
        }
        $editorLink = static::getEditorLink(static::findSource());
        $this->attributes['count']++;
        $this->attributes['totalTime'] += $time;
        $this->attributes['queries'][] = compact('runnableSql', 'dumpSql', 'explain', 'time', 'name', 'editorLink');
    }

    private function createRunnableSql($prepare, $bindings)
    {
        $prepare = str_replace(['%', '?'], ['%%', '%s'], $prepare);
        $sql = vsprintf($prepare, $bindings);

        return $sql;
    }

    private function getExplain($sql, $bindings, $pdo)
    {
        if (stripos($sql, 'select') === 0) {
            $statement = $pdo->prepare('EXPLAIN '.$sql);
            $statement->execute($bindings);

            return $explain = $statement->fetchAll(PDO::FETCH_CLASS);
        } else {
            return [];
        }
    }

    /**
     * Returns syntax highlighted SQL command.
     *
     * @param  string
     *
     * @return string
     */
    private static function dumpSql($sql, array $params = null, $connection = null)
    {
        static $keywords1 = 'SELECT|(?:ON\s+DUPLICATE\s+KEY)?UPDATE|INSERT(?:\s+INTO)?|REPLACE(?:\s+INTO)?|DELETE|CALL|UNION|FROM|WHERE|HAVING|GROUP\s+BY|ORDER\s+BY|LIMIT|OFFSET|SET|VALUES|LEFT\s+JOIN|INNER\s+JOIN|TRUNCATE';
        static $keywords2 = 'ALL|DISTINCT|DISTINCTROW|IGNORE|AS|USING|ON|AND|OR|IN|IS|NOT|NULL|[RI]?LIKE|REGEXP|TRUE|FALSE';

        // insert new lines
        $sql = " $sql ";
        $sql = preg_replace("#(?<=[\\s,(])($keywords1)(?=[\\s,)])#i", "\n\$1", $sql);

        // reduce spaces
        $sql = preg_replace('#[ \t]{2,}#', ' ', $sql);

        $sql = wordwrap($sql, 100);
        $sql = preg_replace('#([ \t]*\r?\n){2,}#', "\n", $sql);

        // syntax highlight
        $sql = htmlSpecialChars($sql, ENT_IGNORE, 'UTF-8');
        $sql = preg_replace_callback("#(/\\*.+?\\*/)|(\\*\\*.+?\\*\\*)|(?<=[\\s,(])($keywords1)(?=[\\s,)])|(?<=[\\s,(=])($keywords2)(?=[\\s,)=])#is", function ($matches) {
            if (! empty($matches[1])) { // comment
                return '<em style="color:gray">'.$matches[1].'</em>';
            } elseif (! empty($matches[2])) { // error
                return '<strong style="color:red">'.$matches[2].'</strong>';
            } elseif (! empty($matches[3])) { // most important keywords
                return '<strong style="color:blue; text-transform: uppercase;">'.$matches[3].'</strong>';
            } elseif (! empty($matches[4])) { // other keywords
                return '<strong style="color:green">'.$matches[4].'</strong>';
            }
        }, $sql);

        // parameters
        $sql = preg_replace_callback('#\?#', function () use ($params, $connection) {
            static $i = 0;
            if (! isset($params[$i])) {
                return '?';
            }
            $param = $params[$i++];
            if (is_string($param) && (preg_match('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u', $param) || preg_last_error())) {
                return '<i title="Length '.strlen($param).' bytes">&lt;binary&gt;</i>';
            } elseif (is_string($param)) {
                $text = htmlspecialchars($connection ? $connection->quote($param) : '\''.$param.'\'', ENT_NOQUOTES, 'UTF-8');

                return '<span title="Length '.$length.' characters">'.$text.'</span>';
            } elseif (is_resource($param)) {
                $type = get_resource_type($param);
                if ($type === 'stream') {
                    $info = stream_get_meta_data($param);
                }

                return '<i'.(isset($info['uri']) ? ' title="'.htmlspecialchars($info['uri'], ENT_NOQUOTES, 'UTF-8').'"' : null)
                    .'>&lt;'.htmlSpecialChars($type, ENT_NOQUOTES, 'UTF-8').' resource&gt;</i> ';
            } else {
                return htmlspecialchars($param, ENT_NOQUOTES, 'UTF-8');
            }
        }, $sql);

        return '<pre class="dump">'.trim($sql)."</pre>\n";
    }
}
