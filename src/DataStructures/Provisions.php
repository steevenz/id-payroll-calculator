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

namespace Steevenz\IndonesiaPayrollCalculator\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class Provisions
 * @package Steevenz\IndonesiaPayrollCalculator\DataStructures
 */
class Provisions
{
    /**
     * Provision::$state
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Provisions\State
     */
    public $state;

    /**
     * Provision::$company
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Provisions\Company
     */
    public $company;

    // ------------------------------------------------------------------------

    /**
     * Provisions::__construct
     */
    public function __construct()
    {
        $this->state = new Provisions\State();
        $this->company = new Provisions\Company();
    }
}