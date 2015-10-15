<?php

namespace Recca0120\LaravelTracy\Panels;

use PDO;
use Tracy\Debugger as TracyDebugger;
use Tracy\Helpers as TracyHelpers;

class ConnectionPanel extends AbstractPanel
{
    public $data = [
        'count' => 0,
        'totalTime' => 0,
        'queries' => [],
    ];

    private $count = 0;

    private $totalTime = 0;

    private $queries = [];

    public function __construct($config)
    {
        parent::__construct($config);
        $app = app();
        $events = $app['events'];
        $events->listen('illuminate.query', function ($sql, $bindings, $time, $name) use ($app) {
            $db = $app['db'];
            $connection = $db->connection($name);
            $this->logQuery($sql, $bindings, $time, $connection);
        });
    }

    protected function logQuery($sql, $bindings, $time, $connection)
    {
        ++$this->count;
        $this->totalTime += $time;
        $pdo = $connection->getPdo();
        $bindings = $connection->prepareBindings($bindings);
        $query = $this->createRunnableQuery($sql, $bindings, $pdo);
        $explainSql = self::getExplainSql($sql, $bindings, $pdo);
        $dumpSql = self::dumpSql($query);
        $source = self::findSource();
        $editorLink = self::getEditorLink($source);
        $this->queries[] = compact('sql', 'bindings', 'time', 'connection', 'query', 'explainSql', 'dumpSql', 'source', 'editorLink');

        $this->setData([
            'count' => $this->count,
            'totalTime' => $this->totalTime,
            'queries' => $this->queries,
        ]);
    }

    private static function getExplainSql($sql, $bindings, $pdo)
    {
        $explain = null;
        if (stripos($sql, 'select') === 0) {
            $statement = $pdo->prepare('EXPLAIN '.$sql);
            $statement->execute($bindings);
            $explain = $statement->fetchAll(PDO::FETCH_CLASS);
        }

        return $explain;
    }

    private static function getEditorLink($source)
    {
        $link = null;
        if ($source !== null) {
            $link = substr_replace(TracyHelpers::editorLink($source[0], $source[1]), ' class="nette-DbConnectionPanel-source"', 2, 0);
        }

        return $link;
    }

    private static function createRunnableQuery($query, $bindings, $pdo)
    {
        // Format binding data for sql insertion
        // foreach ($bindings as $i => $binding) {
        //     if ($binding instanceof \DateTime) {
        //         $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
        //     } elseif (is_string($binding)) {
        //         $bindings[$i] = "'$binding'";
        //     }
        // }
        // Insert bindings into query
        // $connection->prepareBindings($bindings);
        $query = str_replace(['%', '?'], ['%%', '%s'], $query);
        $query = vsprintf($query, $bindings);

        return $query;
    }
    /**
     * Use a backtrace to search for the origin of the query.
     */
    private static function findSource()
    {
        $source = null;
        $trace = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
        foreach ($trace as $row) {
            if (isset($row['file']) === true && TracyDebugger::getBluescreen()->isCollapsed($row['file']) === false) {
                if ((isset($row['function']) && strpos($row['function'], 'call_user_func') === 0)
                    || (isset($row['class']) && is_subclass_of($row['class'], '\\Illuminate\\Database\\Connection'))
                ) {
                    continue;
                }

                return $source = [$row['file'], (int) $row['line']];
            }
        }

        return $source;
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

    public function toJson()
    {
        $data = $this->data;
        foreach ($data['queries'] as $key => $value) {
            $data['queries'][$key] = $value['query'];
        }

        return array_merge([
            'id' => $this->getClassBasename(),
        ], $data);
    }
}
