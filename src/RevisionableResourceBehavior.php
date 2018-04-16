<?php namespace fortrabbit\AssetBundler;


use yii\base\Behavior;

class RevisionableResourceBehavior extends Behavior
{
    public $revision = null;

    public $revFile = null;

    public $modifiedFiles = [];


    public function getRevision()
    {
        if (!$this->revFile) {
            $this->revFile = $this->owner->basePath . '.rev';
        }

        if ($this->revision) {
            return $this->revision;
        }

        if (file_exists($this->revFile)) {
            $this->revision = file_get_contents($this->revFile);

            return $this->revision;
        }

        // New
        if ($rev = $this->writeRevisionToFile(time())) {
            return $rev;
        }
    }

    /**
     * @param int $revision
     *
     * @return int
     */
    public function writeRevisionToFile(int $revision)
    {
        if (file_put_contents($this->revFile, $revision)) {
            $this->revision = $revision;
        }

        return $this->revision;
    }


    /**
     * @return bool
     */
    public function updateRevisionIfModified()
    {
        // No revision folder?
        if (!is_dir($this->getRevisionResourcePath())) {
            return false;
        }

        // No files in folder?
        if (!($modified = $this->getRecentModificationDate())) {
            return false;
        }

        // No change?
        if ($this->getRevision() >= $modified) {
            return false;
        }

        // Get current revision
        $oldRevPath = $this->getRevisionResourcePath();

        // Write revFile
        $this->writeRevisionToFile($modified);

        // Rename revision folder
        return rename($oldRevPath, $this->getRevisionResourcePath());

    }


    /**
     * @param int $timestamp
     *
     * @return bool
     */
    public function updateRevisionTo(int $timestamp)
    {
        // No revision folder?
        if (!is_dir($this->getRevisionResourcePath())) {
            return false;
        }

        // Get current revision
        $oldRevPath     = $this->getRevisionResourcePath();
        $this->revision = $timestamp;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($oldRevPath),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $this->modifiedFiles[] = str_replace($this->getRevisionResourcePath(), '', $fileinfo->getPathname());
            }
        }

        // Write revFile
        $this->writeRevisionToFile($this->revision);

        // Rename revision folder
        return rename($oldRevPath, $this->getRevisionResourcePath());

    }

    /**
     * @return string
     */
    protected function getRevisionResourcePath()
    {
        return $this->owner->basePath . DIRECTORY_SEPARATOR . $this->getRevision();
    }

    /**
     * Collects modified all files and returns
     * the most recent modification date
     *
     * @return int
     */
    protected function getRecentModificationDate()
    {
        $recent = 0;
        $folder = $this->getRevisionResourcePath();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $mtime = $fileinfo->getMTime();
                if ($mtime > $recent) {
                    $recent = $fileinfo->getMTime();
                }
                if ($mtime > $this->revision) {
                    $this->modifiedFiles[] = str_replace($this->getRevisionResourcePath(), '', $fileinfo->getPathname());
                }

            }
        }

        return $recent;
    }
}
