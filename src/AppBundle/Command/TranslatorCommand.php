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
 * bin/console atico:demo:translator --sheet-name=common --env=dev
 * bin/console atico:demo:translator --env=dev
 */
class TranslatorCommand extends ContainerAwareCommand
{
    /** @var SpreadsheetTranslator */
    private $processor;

    /** @var Translator */
    private $translator;

    protected function configure()
    {
        $this->setName('atico:demo:translator')
            ->setDescription("Translate From an Excel File to Symfony Translation format")
            ->setHelp("Translate From an Excel File to Symfony Translation format")
            ->addOption('sheet-name', null, InputOption::VALUE_OPTIONAL, 'Single Sheet To Translate');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->processor = $this->getContainer()->get('atico.spreadsheet_translator.manager');
        $this->translator = $this->getContainer()->get('translator');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sheetName = $this->buildParamsFromInput($input);
        $this->doExecute($output, $sheetName);
    }

    protected function buildParamsFromInput(InputInterface $input)
    {
        return ($input->hasOption('sheet-name')) ? $input->getOption('sheet-name') : '';
    }

    private function doExecute(OutputInterface $output, $sheetName)
    {
        if (!empty($sheetName)) {
            $this->processor->processSheet($sheetName);
        } else {
            $this->processor->processBook();
        }

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