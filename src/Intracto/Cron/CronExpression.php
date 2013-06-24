<?php

namespace Intracto\Cron;

class CronExpression extends \Cron\CronExpression
{
    /**
     * Output a cron in English text
     *
     * @return string
     */
    public function toEnglish()
    {
        list($minute, $hour, $day, $month, $weekday, $year) = explode(' ', $this->getExpression());

        return
            'At minute  : ' . $this->parse($minute) . '<br>' .
            'At hour    : ' . $this->parse($hour) . '<br>' .
            'At day     : ' . $this->parse($day) . '<br>' .
            'At month   : ' . $this->parse($month) . '<br>' .
            'At weekday : ' . $this->parse($weekday) . '<br>' .
            'At year    : ' . $this->parse($year ?: '*') . '<br>'
            ;
    }

    private function parse($part)
    {
        if ($part == '*') {
            return 'any';
        }

        if (substr($part, 0, 2) == '*/') {
            return 'every ' . $this->parse(substr($part, 2)); /* XXX: recursion */
        }

        preg_match("/\D/is", $part, $list, PREG_OFFSET_CAPTURE);
        $index = $list[0][1];
        if ($index) {
            $return = substr($part, 0, $index);

            $nextPart = substr($part, $index);
            switch ($nextPart[0]) {
                case '-':
                    $return .= ' until ';
                    break;
                case ',':
                    $return .= ' and ';
                    break;
            }
            $return .= $this->parse(substr($nextPart, 1)); /* XXX: recursion */
        } else {
            $return = $part;
        }

        return $return;
    }
}
