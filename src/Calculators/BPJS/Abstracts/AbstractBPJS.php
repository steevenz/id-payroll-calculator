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

namespace Steevenz\IndonesiaPayrollCalculator\BPJS\Abstracts;

// ------------------------------------------------------------------------

/**
 * Class AbstractBPJS
 * @package Steevenz\IndonesiaPayrollCalculator\BPJS\Abstracts
 */
abstract class AbstractBPJS
{
    /**
     * AbstractBPJS::$ump
     *
     * Upah Minimum Propinsi.
     *
     * @var int
     */
    protected $ump = 3940972;

    /**
     * AbstractBPJS::$highestMonthlySalary
     *
     * Batas Tertinggi Upah Minimum Bulanan.
     *
     * @var int
     */
    protected $highestMonthlySalary = 8000000;

    /**
     * AbstractBPJS::$employeeGrossTotalIncome
     *
     * @var float
     */
    protected $employeeGrossTotalIncome = 0;

    // ------------------------------------------------------------------------

    /**
     * AbstractBPJS::setUmp
     *
     * Sets Upah Minimum Propinsi
     *
     * @param $ump
     *
     * @return static
     */
    public function setUmp($ump)
    {
        $this->ump = $ump;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractBPJS::setEmployeeGrossTotalIncome
     *
     * Sets employee gross total income.
     *
     * @param $employeeGrossTotalIncome
     *
     * @return static
     */
    public function setEmployeeGrossTotalIncome($employeeGrossTotalIncome)
    {
        $this->employeeGrossTotalIncome = $employeeGrossTotalIncome;

        return $this;
    }
}