<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Intracto\Cron\CronExpression;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig', array(
        'cron' => '*/15 8-16,03 * * *',
    ));
});

$app->post('/', function (Request $request) use ($app) {
    try {
        $cron = CronExpression::factory($request->get('cron'));

        $nextRuns = 'Next runs  : ';
        foreach ($cron->getMultipleRunDates(10) as $nextRun) {
            $nextRuns .= $nextRun->format('d/m/Y H:i:s') .
                '<br>             ';
        }

        return $app['twig']->render('index.twig', array(
            'cron' => (string) $cron,
            'body' => 'Will run   :<br>' .
                $cron->toEnglish() . '<br>' .
                'Last run   : ' . $cron->getPreviousRunDate()->format('d/m/Y H:i:s') . '<br>' .
                $nextRuns
        ));
    } catch (\Exception $e) {
        return $app['twig']->render('index.twig', array(
            'cron' => '*/15 8-16,03 * * *',
        ));
    }
});

$app->run();
