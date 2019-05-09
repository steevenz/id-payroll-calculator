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

namespace Steevenz\IndonesiaPayrollCalculator\DataStructures\Provisions;

// ------------------------------------------------------------------------

/**
 * Class Company
 * @package Steevenz\IndonesiaPayrollCalculator\DataStructures\Provisions
 */
class Company
{
    /**
     * Company::$numOfWorkingDays
     *
     * @var int
     */
    public $numOfWorkingDays = 25;

    /**
     * Company::$calculateBPJSKesehatan
     *
     * @var bool
     */
    public $calculateBPJSKesehatan = true;

    /**
     * Company::$calculateBPJSKetenagakerjaan
     *
     * @var bool
     */
    public $calculateBPJSKetenagakerjaan = true;

    /**
     * Company::$riskGrade
     *
     * @var int
     */
    public $riskGrade = 2;

    /**
     * Company::$absentPenalty
     *
     * @var int
     */
    public $absentPenalty = 0;

    /**
     * Company::$latetimePenalty
     *
     * @var int
     */
    public $latetimePenalty = 0;
}