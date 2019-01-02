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
 * Single Sheet: bin/console atico:demo:translator --sheet-name=common --book-name=frontend --env=dev
 * Full Book: bin/console atico:demo:translator --env=dev
 * Error: bin/console atico:demo:translator --sheet-name=common --env=dev
 */
class TranslatorCommand extends ContainerAwareCommand
{
    /** @var SpreadsheetTranslator */
    private $processor;

    /** @var Translator */
    private $translator;

    private $sheetName;
    private $bookName;

    protected function configure()
    {
        $this->setName('atico:demo:translator')
            ->setDescription("Translate From an Excel File to Symfony Translation format")
            ->setHelp("Translate From an Excel File to Symfony Translation format")
            ->addOption('sheet-name', null, InputOption::VALUE_OPTIONAL, 'Single Sheet To Translate')
            ->addOption('book-name', null, InputOption::VALUE_OPTIONAL, 'Book name To Translate (Domain)');
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
        $this->sheetName = $input->hasOption('sheet-name') ? $input->getOption('sheet-name') : null;
        $this->bookName = $input->hasOption('book-name') ? $input->getOption('book-name') : null;
    }

    /**
     * @throws \Exception
     */
    protected function checkParamsConsistency()
    {
        if (!empty($this->sheetName) && empty($this->bookName)) {
            throw new \Exception('book parameter is required for a given sheet');
        }
    }

    /**
     * @throws \Exception
     */
    private function doExecute(OutputInterface $output)
    {
        if (!empty($this->sheetName)) {
            $this->processor->processSheet($this->sheetName, $this->bookName);
        } elseif (!empty($this->bookName)) {
            $this->processor->processBook($this->bookName);
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