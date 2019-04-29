<?php
declare(strict_types=1);

namespace Paysera\PhpStormHelper\Tests\Service;

use Paysera\PhpStormHelper\Service\GitignoreHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class GitignoreHelperTest extends TestCase
{
    /**
     * @param string $expected
     * @param string $original
     * @param array $rules
     *
     * @dataProvider provideGitignoreData
     */
    public function testSetupGitignore(string $expected, string $original, array $rules)
    {
        $filesystem = new Filesystem();

        $helper = new GitignoreHelper($filesystem, $rules);

        $targetDirectory = __DIR__ . '/../output/gitignore/';
        $target = $targetDirectory . '.gitignore';

        mkdir($targetDirectory);

        try {
            $filesystem->dumpFile($target, $original);

            $helper->setupGitignore($target);

            $this->assertStringEqualsFile($target, $expected);
        } finally {
            $filesystem->remove($targetDirectory);
        }
    }

    public function provideGitignoreData()
    {
        return [
            [
                "first\nsecond\n# comment\n\nthird\n.idea/*.iml\n# configuration\nworkspace.xml\n",
                "first\n.idea/\nsecond\n# comment\n\nthird",
                [
                    '.idea/*.iml',
                    '# configuration',
                    'workspace.xml',
                ],
            ],
            [
                "first\n",
                "first\n.idea/\n/.idea/*\n/.idea/\n.idea",
                [],
            ],
            [
                "first\n",
                "first\n.idea/",
                ['first'],
            ],
        ];
    }
}
