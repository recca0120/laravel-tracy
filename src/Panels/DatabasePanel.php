<?php

namespace Recca0120\LaravelTracy\Panels;

use DateTime;
use Exception;
use PDO;

class DatabasePanel extends AbstractPanel
{
    /**
     * $queries.
     *
     * @var array
     */
    protected $queries = [];

    /**
     * $totalTime.
     *
     * @var float
     */
    protected $totalTime = 0.0;

    /**
     * $counter.
     *
     * @var int
     */
    protected $counter = 0;

    /**
     * subscribe.
     *
     * @method subscribe
     */
    public function subscribe()
    {
        $eventName = $this->getEventName();
        $this->laravel['events']->listen($eventName, function ($event) use ($eventName) {
            if ($eventName === 'illuminate.query') {
                list($sql, $bindings, $time, $name) = func_get_args();
                $connection = $this->laravel['db']->connection($name);
                $pdo = $connection->getPdo();
            } else {
                $sql = $event->sql;
                $bindings = $event->bindings;
                $time = $event->time;
                $name = $event->connectionName;
                $pdo = $event->connection->getPdo();
            }

            $this->logQuery($sql, $bindings, $time, $name, $pdo);
        });
    }

    /**
     * getEventName.
     *
     * @method getEventName
     *
     * @return string
     */
    public function getEventName()
    {
        return (version_compare($this->laravel->version(), 5.2, '>=') === true) ?
            'Illuminate\Database\Events\QueryExecuted' : 'illuminate.query';
    }

    /**
     * logQuery.
     *
     * @method logQuery
     *
     * @param string   $sql
     * @param array    $bindings
     * @param int      $time
     * @param string   $name
     * @param PDO      $pdo
     * @param string   $driver
     *
     * @return self
     */
    public function logQuery($sql, $bindings = [], $time = 0, $name = null, PDO $pdo = null, $driver = 'mysql')
    {
        $this->counter++;
        $this->totalTime += $time;
        $source = self::findSource();
        $editorLink = self::editorLink($source);
        $this->queries[] = [
            'sql'          => $sql,
            'bindings'     => $bindings,
            'time'         => $time,
            'name'         => $name,
            'pdo'          => $pdo,
            'driver'       => $driver,
            'source'       => $source,
            'editorLink'   => $editorLink,
            'formattedSql' => null,
            'fullSql'      => null,
        ];

        return $this;
    }

    /**
     * prepare sql.
     *
     * @param string $sql
     * @param array $bindings
     *
     * @return string
     */
    public static function prepareBindings($sql, $bindings = [])
    {
        array_walk($bindings, function (&$binding) {
            if ($binding instanceof DateTime) {
                $binding = $binding->format('Y-m-d H:i:s');
            }

            if (is_string($binding) === true) {
                $binding = "'".addslashes($binding)."'";
            }
        });
        $sql = str_replace(['%', '?'], ['%%', '%s'], $sql);
        $sql = vsprintf($sql, $bindings);

        return $sql;
    }

    /**
     * explain sql.
     *
     * @param  |PDO $pdo
     * @param  string $sql
     * @param  array $bindings
     *
     * @return array
     */
    public static function explain(PDO $pdo, $sql, $bindings = [])
    {
        if (preg_match('#\s*\(?\s*SELECT\s#iA', $sql) == true) {
            $statement = $pdo->prepare('EXPLAIN '.$sql);
            $statement->execute($bindings);

            return $statement->fetchAll(PDO::FETCH_CLASS);
        }

        return [];
    }

    /**
     * Returns syntax highlighted SQL command.
     *
     * @param string $sql
     * @param array $params
     * @param \PDO $connection
     *
     * @return string
     */
    public static function formatSql($sql, array $params = null, PDO $connection = null)
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

    /**
     * perform quer analysis hint.
     *
     * @param string $sql
     * @param string $version
     * @param float $driver
     *
     * @return array
     */
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

    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $queries = [];
        foreach ($this->queries as $query) {
            $sql = $query['sql'];
            $bindings = $query['bindings'];
            $pdo = $query['pdo'];
            $driver = $query['driver'];
            $version = 0;

            $fullSql = self::prepareBindings($sql, $bindings);
            $formattedSql = self::formatSql($fullSql);

            $explains = [];
            $hints = [];
            if ($pdo instanceof PDO) {
                try {
                    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
                } catch (Exception $e) {
                    $driver = null;
                }

                if ($driver === 'mysql') {
                    $explains = static::explain($pdo, $sql, $bindings);
                    try {
                        $version = $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
                    } catch (Exception $e) {
                    }
                    $hints = static::performQueryAnalysis($fullSql, $version, $driver);
                }
            }

            $queries[] = array_merge($query, compact('fullSql', 'formattedSql', 'explains', 'hints', 'driver', 'version'));
        }

        return [
            'counter'   => $this->counter,
            'totalTime' => $this->totalTime,
            'queries'   => $queries,
        ];
    }
}
