<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function () {
    $html = wrapHtml('*/15 8-16,03 * * *');

    return new Response($html, 201);
});

$app->post('/', function (Request $request) {
    try {
        $cron = Cron\CronExpression::factory($request->get('cron'));

        $nextRuns = 'Next runs  : ';
        foreach ($cron->getMultipleRunDates(10) as $nextRun) {
            $nextRuns .= $nextRun->format('d/m/Y H:i:s') .
                '<br>             ';
        }

        $html = wrapHtml(
            (string) $cron,
            'Will run   :<br>' .
                cron2eng($cron->getExpression()) . '<br>' .
                'Last run   : ' . $cron->getPreviousRunDate()->format('d/m/Y H:i:s') . '<br>' .
                $nextRuns
        );
    } catch (\Exception $e) {
        $html = wrapHtml('*/15 8-16,03 * * *');
    }

    return new Response($html, 201);
});

$app->run();

function wrapHtml($cron, $body = '')
{
    return
        '<h1>Cron</h1>' .
        '<form method="post" action="/">' .
        '  <input type="text" name="cron" value="' . $cron . '" />' .
        '  <input type="submit" value="Parse">' .
        '</form>' .
        '<pre>' . $body . '</pre>';
}

function cron2eng($cron)
{
    list($minute, $hour, $day, $month, $weekday, $year) = explode(' ', $cron);

    return
        'At minute  : ' . parse($minute) . '<br>' .
        'At hour    : ' . parse($hour) . '<br>' .
        'At day     : ' . parse($day) . '<br>' .
        'At month   : ' . parse($month) . '<br>' .
        'At weekday : ' . parse($weekday) . '<br>' .
        'At year    : ' . parse($year ?: '*') . '<br>'
    ;
}

function parse($part)
{
    if ($part == '*') {
        return 'any';
    }

    if (substr($part, 0, 2) == '*/') {
        return 'every ' . parse(substr($part, 2));
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
        $return .= parse(substr($nextPart, 1));
    } else {
       $return = $part;
    }

    return $return;
}
