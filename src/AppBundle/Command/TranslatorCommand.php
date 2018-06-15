<?php

/*
 * This file is part of the Atico/SpreadsheetTranslator package.
 *
 * (c) Samuel Vicent <samuelvicent@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Command;

use Atico\SpreadsheetTranslator\Core\SpreadsheetTranslator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * Reference command:
 * bin/console atico:demo:translator --sheet=common --book=frontend --env=dev
 * bin/console atico:demo:translator --env=dev
 */
class TranslatorCommand extends ContainerAwareCommand
{
    /** @var SpreadsheetTranslator */
    private $processor;

    /** @var Translator */
    private $translator;

    private $sheet;
    private $book;

    protected function configure()
    {
        $this->setName('atico:demo:translator')
            ->setDescription("Translate From an Excel File to Symfony Translation format")
            ->setHelp("Translate From an Excel File to Symfony Translation format")
            ->addOption('sheet', null, InputOption::VALUE_OPTIONAL, 'Single Sheet To Translate')
            ->addOption('book', null, InputOption::VALUE_OPTIONAL, 'Single Book To Translate');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->processor = $this->getContainer()->get('atico.spreadsheet_translator.manager');
        $this->translator = $this->getContainer()->get('translator');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->buildParamsFromInput($input);
        $this->checkParamsConsistency();
        $this->doExecute($output);
    }

    protected function buildParamsFromInput(InputInterface $input)
    {
        $this->sheet = $input->hasOption('sheet') ? $input->getOption('sheet') : null;
        $this->book = !$input->hasOption('book') ? $input->getOption('book') : null;
    }

    /**
     * @throws \Exception
     */
    protected function checkParamsConsistency()
    {
        if (!empty($this->sheet) && empty($this->book)) {
            throw new \Exception('book parameter is required for a given sheet');
        }
    }

    private function doExecute(OutputInterface $output)
    {
        if (!empty($this->sheet)) {
            $this->processor->processSheet($this->sheet, $this->book);
        } elseif (!empty($this->book)) {
            $this->processor->processBook($this->book);
        }

        $this->processor->processAllBooks();

        $this->showTranslatedFragment($output);
    }

    private function showTranslatedFragment(OutputInterface $output)
    {
        $locale = 'es_ES';
        $sectionSubsection = 'homepage.title';
        $translationDomain = 'demo_frontend';

        $this->translator->setFallbackLocales(['en', $locale]);
        $output->writeln(
            sprintf(
                'Translation text for "%s" in "%s": "%s"',
                $sectionSubsection,
                $locale,
                $this->translator->trans($sectionSubsection, [], $translationDomain))
        );
    }
}