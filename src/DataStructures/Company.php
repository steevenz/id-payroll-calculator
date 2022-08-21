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

namespace IrwanRuntuwene\IndonesiaPayrollCalculator\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class Company
 * @package IrwanRuntuwene\IndonesiaPayrollCalculator\DataStructures
 */
class Company
{
    /**
     * Company::$allowances
     *
     * @var \IrwanRuntuwene\IndonesiaPayrollCalculator\DataStructures\Employee\Allowances
     */
    public $allowances;

    // ------------------------------------------------------------------------

    /**
     * Employee::__construct
     */
    public function __construct()
    {
        $this->allowances = new Company\Allowances();
    }
}