<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Atico\SpreadsheetTranslator\Core\SpreadsheetTranslator;
use App\Command\TranslatorCommand;
use App\Tests\Fixtures\TestTranslator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class TranslatorCommandTest extends TestCase
{
    private SpreadsheetTranslator $spreadsheetTranslator;

    private TestTranslator $translator;

    private TranslatorCommand $command;

    protected function setUp(): void
    {
        $this->spreadsheetTranslator = $this->createMock(SpreadsheetTranslator::class);
        $this->translator = new TestTranslator();
        $this->command = new TranslatorCommand($this->spreadsheetTranslator, $this->translator);
    }

    public function testExecuteWithBothOptions(): void
    {
        $sheetName = 'common';
        $bookName = 'frontend';

        $this->spreadsheetTranslator
            ->expects($this->once())
            ->method('processSheet')
            ->with($sheetName, $bookName);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            '--sheet-name' => $sheetName,
            '--book-name' => $bookName,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Translation text for "homepage_title"', $commandTester->getDisplay());
        $this->assertStringContainsString('Translated text', $commandTester->getDisplay());
    }

    public function testExecuteWithoutOptions(): void
    {
        $this->spreadsheetTranslator
            ->expects($this->once())
            ->method('processSheet')
            ->with('', '');

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testExecuteWithOnlySheetName(): void
    {
        $sheetName = 'common';

        $this->spreadsheetTranslator
            ->expects($this->once())
            ->method('processSheet')
            ->with($sheetName, '');

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            '--sheet-name' => $sheetName,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testExecuteWithOnlyBookName(): void
    {
        $bookName = 'frontend';

        $this->spreadsheetTranslator
            ->expects($this->once())
            ->method('processSheet')
            ->with('', $bookName);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            '--book-name' => $bookName,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testCommandName(): void
    {
        $this->assertSame('atico:demo:translator', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertSame(
            'Translate From an Excel File to Symfony Translation format',
            $this->command->getDescription()
        );
    }

    public function testCommandHasSheetNameOption(): void
    {
        $definition = $this->command->getDefinition();
        $this->assertTrue($definition->hasOption('sheet-name'));

        $option = $definition->getOption('sheet-name');
        $this->assertSame('Single Sheet To Translate', $option->getDescription());
        $this->assertFalse($option->isValueRequired());
    }

    public function testCommandHasBookNameOption(): void
    {
        $definition = $this->command->getDefinition();
        $this->assertTrue($definition->hasOption('book-name'));

        $option = $definition->getOption('book-name');
        $this->assertSame('Book name To Translate (Domain)', $option->getDescription());
        $this->assertFalse($option->isValueRequired());
    }

    public function testBuildParamsFromInputReturnsCorrectStructure(): void
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            '--sheet-name' => 'test-sheet',
            '--book-name' => 'test-book',
        ]);

        // Verify command executed successfully (indirectly tests buildParamsFromInput)
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
