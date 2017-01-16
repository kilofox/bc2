<?php

/**
 * STDERR log writer. Writes out messages to STDERR.
 *
 * @package    Bootphp
 * @category   Logging
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (c) 2008-2014 Kohana Team
 * @license    http://kilofox.net/license
 */
class Kohana_Log_StdErr extends Log_Writer
{
    /**
     * Writes each of the messages to STDERR.
     *
     *     $writer->write($messages);
     *
     * @param   array   $messages
     * @return  void
     */
    public function write(array $messages)
    {
        foreach ($messages as $message) {
            // Writes out each message
            fwrite(STDERR, $this->format_message($message) . PHP_EOL);
        }
    }

}
