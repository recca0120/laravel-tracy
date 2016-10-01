<?php

namespace Recca0120\LaravelTracy;

use SessionHandlerInterface;

class SessionHandlerWrapper implements SessionHandlerInterface
{
    /**
     * @var SessionHandlerInterface
     */
    private $handler;

    public function __construct(SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Close the session.
     * @link http://php.net/manual/en/sessionhandlerinterface.close.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function close()
    {
        $this->handler->close();

        return true;
    }

    /**
     * Destroy a session.
     * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
     * @param string $sessionId The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function destroy($sessionId)
    {
        $this->handler->destroy($sessionId);

        return true;
    }

    /**
     * Cleanup old sessions.
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     * @param int $maxLifeTime <p>
     * Sessions that have not updated for
     * the last maxlifetime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function gc($maxLifeTime)
    {
        $this->handler->gc($maxLifeTime);

        return true;
    }

    /**
     * Initialize session.
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     * @param string $savePath The path where to store/retrieve the session.
     * @param string $name The session name.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function open($savePath, $name)
    {
        $this->handler->open($savePath, $name);

        return true;
    }

    /**
     * Read session data.
     * @link http://php.net/manual/en/sessionhandlerinterface.read.php
     * @param string $sessionId The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function read($sessionId)
    {
        return $this->handler->read($sessionId);
    }

    /**
     * Write session data.
     * @link http://php.net/manual/en/sessionhandlerinterface.write.php
     * @param string $sessionId The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function write($sessionId, $session_data)
    {
        $this->handler->write($sessionId, $session_data);

        return true;
    }
}
