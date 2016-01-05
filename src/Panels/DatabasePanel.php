<?php

namespace Recca0120\LaravelTracy\Panels;

use PDO;
use Recca0120\LaravelTracy\Helper;

class DatabasePanel extends AbstractPanel
{
    protected $db;

    protected $attributes = [
        'count'     => 0,
        'totalTime' => 0,
        'logs'      => [],
    ];

    public function subscribe()
    {
        $this->db = $this->app['db'];
        $eventName = $this->getEventName();
        $this->app['events']->listen($eventName, function ($event) use ($eventName) {
            if ($eventName === 'illuminate.query') {
                list($sql, $bindings, $time, $name) = func_get_args();
                $connection = $db->connection($name);
            } else {
                $sql = $event->sql;
                $bindings = $event->bindings;
                $time = $event->time;
                $connection = $event->connection;
                $name = $event->connectionName;
            }
            call_user_func_array([$this, 'logQuery'], compact('sql', 'bindings', 'time', 'name', 'connection'));
        });
    }

    protected function getEventName()
    {
        if (method_exists($this->app, 'bindShared') === false) {
            return 'Illuminate\Database\Events\QueryExecuted';
        }

        return 'illuminate.query';
    }

    public function logQuery($prepare, $bindings, $time, $name, $connection)
    {
        $driver = $connection->getDriverName();
        $pdo = $connection->getPdo();
        $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);

        $sql = static::prepareBinding($prepare, $bindings);
        $formattedSql = static::formatSql($sql);
        $hints = static::performQueryAnalysis($sql, $version, $driver);
        $editorLink = Helper::getEditorLink(Helper::findSource());

        $explains = [];
        if ($driver === 'mysql') {
            $explains = $this->explains($prepare, $bindings, $pdo);
        }

        $this->attributes['count']++;
        $this->attributes['totalTime'] += $time;
        $this->attributes['logs'][] = compact('sql', 'formattedSql', 'hints', 'explains', 'editorLink', 'prepare', 'bindings', 'time', 'name');
    }

    private static function prepareBinding($prepare, $bindings)
    {
        $prepare = str_replace(['%', '?'], ['%%', '%s'], $prepare);
        $sql = vsprintf($prepare, $bindings);

        return $sql;
    }

    private static function explains($prepare, $bindings, $pdo)
    {
        if (stripos($prepare, 'select') === 0) {
            $statement = $pdo->prepare('EXPLAIN '.$prepare);
            $statement->execute($bindings);

            return $statement->fetchAll(PDO::FETCH_CLASS);
        }

        return [];
    }

    /**
     * Returns syntax highlighted SQL command.
     *
     * @param  string
     *
     * @return string
     */
    private static function formatSql($sql, array $params = null, $connection = null)
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
            if (!empty($matches[1])) { // comment
                return '<em style="color:gray">'.$matches[1].'</em>';
            } elseif (!empty($matches[2])) { // error
                return '<strong style="color:red">'.$matches[2].'</strong>';
            } elseif (!empty($matches[3])) { // most important keywords
                return '<strong style="color:blue; text-transform: uppercase;">'.$matches[3].'</strong>';
            } elseif (!empty($matches[4])) { // other keywords
                return '<strong style="color:green">'.$matches[4].'</strong>';
            }
        }, $sql);

        // parameters
        $sql = preg_replace_callback('#\?#', function () use ($params, $connection) {
            static $i = 0;
            if (!isset($params[$i])) {
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

    public static function performQueryAnalysis($sql, $version = null, $driver = null)
    {
        $hints = [];
        if (preg_match('/^\\s*SELECT\\s*`?[a-zA-Z0-9]*`?\\.?\\*/i', $sql)) {
            $hints[] = 'Use <code>SELECT *</code> only if you need all columns from table';
        }
        if (preg_match('/ORDER BY RAND()/i', $sql)) {
            $hints[] = '<code>ORDER BY RAND()</code> is slow, try to avoid if you can.
                You can <a href="http://stackoverflow.com/questions/2663710/how-does-mysqls-order-by-rand-work">read this</a>
                or <a href="http://stackoverflow.com/questions/1244555/how-can-i-optimize-mysqls-order-by-rand-function">this</a>';
        }
        if (strpos($sql, '!=') !== false) {
            $hints[] = 'The <code>!=</code> operator is not standard. Use the <code>&lt;&gt;</code> operator to test for inequality instead.';
        }
        if (stripos($sql, 'WHERE') === false) {
            $hints[] = 'The <code>SELECT</code> statement has no <code>WHERE</code> clause and could examine many more rows than intended';
        }
        if (preg_match('/LIMIT\\s/i', $sql) && stripos($sql, 'ORDER BY') === false) {
            $hints[] = '<code>LIMIT</code> without <code>ORDER BY</code> causes non-deterministic results, depending on the query execution plan';
        }
        if (preg_match('/LIKE\\s[\'"](%.*?)[\'"]/i', $sql, $matches)) {
            $hints[] = 'An argument has a leading wildcard character: <code>'.$matches[1].'</code>.
                                The predicate with this argument is not sargable and cannot use an index if one exists.';
        }
        if ($version < 5.5 && $driver === 'mysql') {
            if (preg_match('/\\sIN\\s*\\(\\s*SELECT/i', $sql)) {
                $hints[] = '<code>IN()</code> and <code>NOT IN()</code> subqueries are poorly optimized in that MySQL version : '.$version.
                                    '. MySQL executes the subquery as a dependent subquery for each row in the outer query';
            }
        }

        return $hints;
    }
}
