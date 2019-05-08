<?php
/**
 * This file is part of the Payroll Calculator Package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace Steevenz\IndonesiaPayrollCalculator\PPh;

// ------------------------------------------------------------------------

/**
 * Class PPh23
 * @package Steevenz\IndonesiaPayrollCalculator
 */
class PPh23
{
    /**
     * PPh23::$companyHasNPWP
     *
     * @var bool
     */
    protected $companyHasNPWP = true;

    /**
     * PPh23::$employeeHasNPWP
     *
     * @var bool
     */
    protected $employeeHasNPWP = true;

    /**
     * PPh23::$royalty
     *
     * @var float
     */
    protected $royalty = 0;

    // ------------------------------------------------------------------------

    /**
     * PPh23::setCompanyHasNPWP
     *
     * @param bool $hasNPWP
     *
     * @return static
     */
    public function setCompanyHasNPWP($hasNPWP)
    {
        $this->companyHasNPWP = (bool) $hasNPWP;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PPh23::setEmployeeHasNPWP
     *
     * @param bool $hasNPWP
     *
     * @return static
     */
    public function setEmployeeHasNPWP($hasNPWP)
    {
        $this->employeeHasNPWP = (bool) $hasNPWP;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PPh23::calculate
     *
     * @return float
     */
    public function calculate()
    {
        if ($this->companyHasNPWP === true and $this->employeeHasNPWP === true) {
            return $this->royalty * (15 / 100);
        } elseif($this->companyHasNPWP === false and $this->employeeHasNPWP === true) {
            return (200 / 100) * (2 / 100) * $this->royalty;
        }
    }
}