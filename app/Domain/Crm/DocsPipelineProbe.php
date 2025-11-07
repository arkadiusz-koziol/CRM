<?php

namespace App\Domain\Crm;

/**
 * Documentation Pipeline Probe
 * 
 * This class is used for smoke testing the automated documentation pipeline.
 * It provides a simple method that returns true to verify that the docs:scope
 * command can detect changes in the app/Domain/Crm/ directory and map them
 * to the appropriate documentation sections.
 * 
 * This is a test-only class and has no impact on runtime behavior.
 */
class DocsPipelineProbe
{
    /**
     * Health check method for documentation pipeline testing.
     * 
     * @return bool Always returns true to indicate the probe is healthy.
     */
    public function isDocsPipelineHealthy(): bool
    {
        return true;
    }
}
