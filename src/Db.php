<?php

use AEngine\Orchid\Database\Exception\DatabaseException;

/**
 * Db is wrap around PDO with support Master-Slave random select server
 */
class Db
{
    /**
     * Array of connections
     *
     * @var array
     */
    protected static $connection = [];

    /**
     * @var PDO
     */
    protected static $lastConnection;

    /**
     * @var PDOStatement
     */
    public static $lastQuery;

    /**
     * Setup the Database
     *
     * @param array $configs
     *
     * @throws RuntimeException
     * @throws DatabaseException
     */
    public static function setup(array $configs = [])
    {
        if ($configs) {
            $default = [
                'dsn'      => '',
                'username' => '',
                'password' => '',
                'options'  => [],
                'role'     => 'master',
            ];

            foreach ($configs as $index => $config) {
                $config = array_merge($default, $config);

                static::$connection[$config['role'] == 'master' ? 'master' : 'slave'][] = function () use ($config) {
                    try {
                        return new PDO(
                            $config['dsn'],
                            $config['username'],
                            $config['password'],
                            $config['options']
                        );
                    } catch (PDOException $ex) {
                        throw new DatabaseException(
                            'The connection to the database server fails (' . $ex->getMessage() . ')',
                            0,
                            $ex
                        );
                    }
                };
            }

            return;
        }

        throw new RuntimeException('There are no settings to connect to the memory');
    }

    /**
     * Returns PDO object
     *
     * @param bool $use_master
     *
     * @return PDO
     * @throws UnexpectedValueException
     */
    public static function getConnection($use_master = false)
    {
        $pool = [];
        $role = $use_master ? 'master' : 'slave';

        switch (true) {
            case !empty(static::$connection[$role]): {
                $pool = static::$connection[$role];
                break;
            }
            case !empty(static::$connection['master']): {
                $pool = static::$connection['master'];
                $role = 'master';
                break;
            }
            case !empty(static::$connection['slave']): {
                $pool = static::$connection['slave'];
                $role = 'slave';
                break;
            }
        }

        if ($pool) {
            if (is_array($pool)) {
                return static::$connection[$role] = $pool[array_rand($pool)]();
            } else {
                return $pool;
            }
        }

        throw new UnexpectedValueException('Unable to establish connection');
    }

    /**
     * Prepares and executes a database query
     *
     * @param string $query
     * @param array  $params
     * @param bool   $use_master
     *
     * @return PDOStatement
     */
    public static function query($query, array $params = [], $use_master = false)
    {
        // obtain connection
        static::$lastConnection = static::getConnection(
            !$use_master ? (strncmp($query, 'SELECT', 6) || strncmp($query, '(SELECT', 6)) : true
        );

        static::$lastQuery = static::$lastConnection->prepare($query);
        static::$lastQuery->execute($params);

        return static::$lastQuery;
    }

    /**
     * Returns the ID of the last inserted row
     *
     * @return string
     */
    public static function lastInsertId()
    {
        return static::$lastConnection->lastInsertId();
    }
}
