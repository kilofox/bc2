<?php

/**
 * STDOUT log writer. Writes out messages to STDOUT.
 *
 * @package    Bootphp
 * @category   Logging
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (c) 2008-2014 Kohana Team
 * @license    http://kilofox.net/license
 */
class Kohana_Log_StdOut extends Log_Writer
{
    /**
     * Writes each of the messages to STDOUT.
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
            fwrite(STDOUT, $this->format_message($message) . PHP_EOL);
        }
    }

}
