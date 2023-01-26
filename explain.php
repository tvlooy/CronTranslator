<?php

require __DIR__.'/vendor/autoload.php';

use App\Cron\CronExpression;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->addArgument(
        'schedule',
        InputArgument::OPTIONAL,
        "A cron schedule"
    )
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $schedule = (string) $input->getArgument('schedule');
        if ('' === $schedule) {
            $schedule = (string) $this->getHelper('question')->ask($input, $output, new Question("Cron schedule:\n> "));
        }
        $output->writeln('For cron '.$schedule."\n");

        $cron = new CronExpression($schedule);

        foreach ($cron->toEnglish() as $line) {
            $output->writeln('    '.$line);
        }
        $output->writeln("\n".'    '.'Last run   : ' . $cron->getPreviousRunDate()->format('d/m/Y H:i:s'));

        $output->write('    '.'Next runs  : ');
        $prefix = '';
        foreach ($cron->getMultipleRunDates(10) as $nextRun) {
            $output->writeln($prefix.$nextRun->format('d/m/Y H:i:s'));
            $prefix = '                 ';
        }
        $output->writeln("\n");

        return Command::SUCCESS;
    })->run();

