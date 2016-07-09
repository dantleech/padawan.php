<?php

namespace Padawan\Framework\Generator;

use Padawan\Framework\Utils\PathResolver;
use Padawan\Domain\Project;
use Padawan\Domain\Generator\FilesFinder as FilesFinderInterface;

class FilesFinder implements FilesFinderInterface
{
    /** @var PathResolver */
    private $path;

    public function __construct(PathResolver $path)
    {
        $this->path = $path;
    }

    public function findProjectFiles(Project $project)
    {
        $finder = new Finder();
        $finder->name('*.php');
        $finder->ignoreVCS(true);
        $finder->notPath('/.*\/.*vendor/');
        $finder->in($project->getRootDir());

        $files = [];

        foreach ($finder as $fileInfo) {
            $files[] = $this->path->relative($project->getRootDir(), $fileInfo->getPathname());
        }

        return $files; 
    }

    public function findChangedProjectFiles(Project $project)
    {
        throw new \Exception("Not implemented yet");
    }
}
