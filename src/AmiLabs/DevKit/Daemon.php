<?php

namespace AmiLabs\DevKit;

abstract class Daemon
{
    /**
     * @var array
     */
    protected $aState;

    /**
     * Returns path to PHP-file containing last daemon state.
     *
     * @param  array $aOptions  Any options
     * @return string
     */
    abstract protected function getStatePath($aOptions = array());

    /**
     * Loads state from PHP-file.
     *
     * @param  array $aDefault  Default state
     * @param  array $aOptions  Any options passed to Daemon::getStatePath()
     * @return void
     */
    protected function loadState(array $aDefault = array(), $aOptions = array())
    {
        $path = $this->getStatePath($aOptions);
        $this->aState =
            file_exists($path)
                ? require($path)
                : $aDefault;
    }

    /**
     * Saves state to PHP-file.
     *
     * @param  array $aOptions  Any options passed to Daemon::getStatePath()
     * @return void
     * @throws \ErrorException  If cannot save state
     */
    protected function saveState(array $aOptions = array())
    {
        $path = $this->getStatePath($aOptions);
        $result = file_put_contents(
            $path,
            "<" . "?php\n\nreturn " . var_export($this->aState, TRUE) . ";\n\n"
        );
        if(FALSE === $result){
            throw new \ErrorException("Cannot save daemon state to '" . $path . "'");
        }
    }
}
