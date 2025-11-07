<?php

namespace App\Domain\Crm;

/**
 * Smoke test probe for documentation pipeline E2E testing.
 * 
 * This class is used to verify that the documentation pipeline
 * correctly detects and documents changes in the Crm domain.
 */
class DocsPipelineProbe2
{
    /**
     * Health check method for documentation pipeline v2.
     * 
     * @return bool Always returns true to indicate healthy state
     */
    public function isDocsPipelineHealthyV2(): bool
    {
        return true;
    }
}

