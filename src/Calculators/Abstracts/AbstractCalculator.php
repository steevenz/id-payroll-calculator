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

namespace Steevenz\IndonesiaPayrollCalculator\Calculators\Abstracts;

// ------------------------------------------------------------------------

use Steevenz\IndonesiaPayrollCalculator\Employee;

/**
 * Class AbstractCalculator
 * @package Steevenz\IndonesiaPayrollCalculator\Calculators\Abstracts
 */
abstract class AbstractCalculator
{
    /**
     * AbstractCalculator::$employee
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\Employee
     */
    protected $employee;

    // ------------------------------------------------------------------------

    /**
     * AbstractCalculator::__construct
     *
     * @param \Steevenz\IndonesiaPayrollCalculator\Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }
}