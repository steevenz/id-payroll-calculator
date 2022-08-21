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

namespace IrwanRuntuwene\IndonesiaPayrollCalculator\Taxes;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayObject;
use IrwanRuntuwene\IndonesiaPayrollCalculator\PayrollCalculator;

/**
 * Class AbstractPph
 * @package IrwanRuntuwene\IndonesiaPayrollCalculator\Taxes
 */
abstract class AbstractPph
{
    /**
     * AbstractPph::$calculator
     *
     * @var PayrollCalculator
     */
    public $calculator;

    /**
     * AbstractPph::$liability
     * 
     * @var \O2System\Spl\DataStructures\SplArrayObject
     */
    public $result;

    // ------------------------------------------------------------------------

    /**
     * AbstractPph::__construct
     *
     * @param \IrwanRuntuwene\IndonesiaPayrollCalculator\PayrollCalculator $calculator
     */
    public function __construct(PayrollCalculator &$calculator)
    {
        $this->calculator =& $calculator;
        $this->result = new SplArrayObject([
            'ptkp' => new SplArrayObject([
                'status' => $this->calculator->provisions->state->getPtkp($this->calculator->employee->numOfDependentsFamily, $this->calculator->employee->maritalStatus),
                'amount' => 0
            ]),
            'pkp' => 0,
            'liability' => new SplArrayObject([
                'rule' => 21,
                'monthly' => 0,
                'annual' => 0
            ])
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPph::getRate
     *
     * @param int $monthlyNetIncome
     *
     * @return float
     */
    public function getRate($monthlyNetIncome)
    {
        $rate = 5;

        if($monthlyNetIncome > 60000000 and $monthlyNetIncome <= 250000000) {
            $rate = 15;
        } elseif($monthlyNetIncome > 250000000 and $monthlyNetIncome <= 500000000) {
            $rate = 25;
        } elseif($monthlyNetIncome > 500000000 and $monthlyNetIncome <= 5000000000) {
            $rate = 30;
        } elseif($monthlyNetIncome > 5000000000) {
            $rate = 35;
        }

        return $rate;
    }

    public function getProgresive($monthlyNetIncome)
    {
        $loop = 1;

        if($monthlyNetIncome > 60000000 and $monthlyNetIncome <= 250000000) {
            $loop = 2;
        } elseif($monthlyNetIncome > 250000000 and $monthlyNetIncome <= 500000000) {
            $loop = 3;
        } elseif($monthlyNetIncome > 500000000 and $monthlyNetIncome <= 5000000000) {
            $loop = 4;
        } elseif($monthlyNetIncome > 5000000000) {
            $loop = 5;
        }

        return $loop;
    }



    // ------------------------------------------------------------------------

    /**
     * AbstractPph::calculate
     * 
     * @return SplArrayObject
     */
    abstract public function calculate();
}
