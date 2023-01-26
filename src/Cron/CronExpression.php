<?php

namespace App\Cron;

class CronExpression extends \Cron\CronExpression
{
    /**
     * Output a cron in English text
     *
     * @return string[]
     */
    public function toEnglish(): array
    {
        [$minute, $hour, $day, $month, $weekday, $year] = array_pad(explode(' ', $this->getExpression()), 6, '*');

        return [
            'At minute  : '.$this->parse($minute),
            'At hour    : '.$this->parse($hour),
            'At day     : '.$this->parse($day),
            'At month   : '.$this->parse($month),
            'At weekday : '.$this->parse($weekday),
            'At year    : '.$this->parse($year),
        ];
    }

    private function parse(string $part): string
    {
        if ('*' === $part) {
            return 'any';
        }

        if (str_starts_with($part, '*/')) {
            return 'every ' . $this->parse(substr($part, 2)); /* XXX: recursion */
        }

        preg_match("/\D/is", $part, $list, PREG_OFFSET_CAPTURE);
        $index = $list[0][1]??'';

        if ('' === $index) {
            return $part;
        }

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

        return  $return.$this->parse(substr($nextPart, 1)); /* XXX: recursion */
    }
}
