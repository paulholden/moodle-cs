<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace MoodleHQ\MoodleCS\moodle\Tests;

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;

// phpcs:disable moodle.NamingConventions

/**
 * Test the TestCaseCoversSniff sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit\TestCaseProviderSniff
 */
class PHPUnitTestCaseProviderTest extends MoodleCSBaseTestCase {

    /**
     * Data provider for self::test_phpunit_test_providers
     */
    public function provider_phpunit_data_providers() {
        return [
            'Correct' => [
                'fixture' => 'fixtures/phpunit/provider/correct_test.php',
                'errors' => [],
                'warnings' => [],
            ],
            'Provider Casing' => [
                'fixture' => 'fixtures/phpunit/provider/provider_casing_test.php',
                'errors' => [
                    6 => 'Wrong @dataProvider tag: @dataprovider provided, @dataProvider expected',
                ],
                'warnings' => [
                ],
            ],
            'Provider has Parenthesis' => [
                'fixture' => 'fixtures/phpunit/provider/provider_parents_test.php',
                'errors' => [
                ],
                'warnings' => [
                    6 => 'Data provider should not end with "()". "provider()" provided.',
                ],
            ],
            'Provider Visibility' => [
                'fixture' => 'fixtures/phpunit/provider/provider_visibility_test.php',
                'errors' => [
                    12 => 'Data provider method "provider" must be public.',
                    23 => 'Data provider method "provider_without_visibility" visibility should be specified.',
                    34 => 'Data provider method "static_provider_without_visibility" visibility should be specified.',
                ],
                'warnings' => [
                    17 => 'Data provider method "provider_without_visibility" will need to be converted to static in future.',
                ],
            ],
            'Provider Naming conflicts with test names' => [
                'fixture' => 'fixtures/phpunit/provider/provider_prefix_test.php',
                'errors' => [
                    6 => 'Data provider must not start with "test_". "test_provider" provided.',
                ],
                'warnings' => [
                ],
            ],
            'Static Providers' => [
                'fixture' => 'fixtures/phpunit/provider/static_providers_test.php',
                'errors' => [
                ],
                'warnings' => [
                    12 => 'Data provider method "fixable_provider" will need to be converted to static in future.',
                    23 => 'Data provider method "unfixable_provider" will need to be converted to static in future.',
                    34 => 'Data provider method "partially_fixable_provider" will need to be converted to static in future.',
                ],
            ],
            'Static Providers Applying fixes' => [
                'fixture' => 'fixtures/phpunit/provider/static_providers_fix_test.php',
                'errors' => [
                ],
                'warnings' => [
                    13 => 'Data provider method "fixable_provider" will need to be converted to static in future.',
                    24 => 'Data provider method "unfixable_provider" will need to be converted to static in future.',
                    35 => 'Data provider method "partially_fixable_provider" will need to be converted to static in future.',
                ],
            ],
            'Provider Return Type checks' => [
                'fixture' => 'fixtures/phpunit/provider/provider_returntype_test.php',
                'errors' => [
                    6 => 'Data provider method "provider_no_return" must return an array, a Generator or an Iterable.',
                    17 => 'Data provider method "provider_wrong_return" must return an array, a Generator or an Iterable.',
                    28 => 'Data provider method "provider_returns_generator" must return an array, a Generator or an Iterable.',
                    41 => 'Data provider method "provider_returns_iterator" must return an array, a Generator or an Iterable.',
                ],
                'warnings' => [
                ],
            ],
            'Provider not found' => [
                'fixture' => 'fixtures/phpunit/provider/provider_not_found_test.php',
                'errors' => [
                    6 => 'Data provider method "provider" not found.',
                    14 => 'Wrong @dataProvider tag specified for test test_two, it must be followed by a space and a method name.',
                ],
                'warnings' => [
                ],
            ],
            'Complex test with multiple classes' => [
                'fixture' => 'fixtures/phpunit/provider/complex_provider_test.php',
                'errors' => [
                    7 => 'Data provider method "provider" not found.',
                ],
                'warnings' => [
                    14 => 'Data provider method "second_provider" will need to be converted to static in future.',
                ],
            ],
        ];
    }

    /**
     * Test the moodle.PHPUnit.TestCaseCovers sniff
     *
     * @param string $fixture relative path to fixture to use.
     * @param array $errors array of errors expected.
     * @param array $warnings array of warnings expected.
     * @dataProvider provider_phpunit_data_providers
     */
    public function test_phpunit_test_providers(
        string $fixture,
        array $errors,
        array $warnings
    ): void {
        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.PHPUnit.TestCaseProvider');
        $this->set_fixture(__DIR__ . '/' . $fixture);

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->set_errors($errors);
        $this->set_warnings($warnings);

        // Let's do all the hard work!
        $this->verify_cs_results();
    }
}
