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

use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Pph21
 * @package IrwanRuntuwene\IndonesiaPayrollCalculator\Taxes
 */
class Pph21 extends AbstractPph
{
    /**
     * PPh21::calculate
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function calculate()
    {
        /**
         * PPh21 dikenakan bagi yang memiliki penghasilan lebih dari 4500000
         */
        if($this->calculator->result->earnings->nett > 4500000) {
            // Annual PTKP base on number of dependents family
            $this->result->ptkp->amount = $this->calculator->provisions->state->getPtkpAmount($this->calculator->employee->numOfDependentsFamily, $this->calculator->employee->maritalStatus);

            // Annual PKP (Pajak Atas Upah)
            if($this->calculator->employee->earnings->holidayAllowance > 0 && $this->calculator->employee->bonus->getSum() == 0) {
                // Pajak Atas Upah
                $earningTax = ($this->calculator->result->earnings->annualy->nett - $this->result->ptkp->amount) * ($this->getRate($this->calculator->result->earnings->nett) / 100);

                // Penghasilan + THR Kena Pajak
                $this->result->pkp = ($this->calculator->result->earnings->annualy->nett + $this->calculator->employee->earnings->holidayAllowance) - $this->result->ptkp->amount;

                $this->result->liability->annual = $this->result->pkp - $earningTax;
            } elseif($this->calculator->employee->earnings->holidayAllowance > 0 && $this->calculator->employee->bonus->getSum() > 0) {
                // Pajak Atas Upah
                $earningTax = ($this->calculator->result->earnings->annualy->nett - $this->result->ptkp->amount) * ($this->getRate($this->calculator->result->earnings->nett) / 100);

                // Penghasilan + THR Kena Pajak
                $this->result->pkp = ($this->calculator->result->earnings->annualy->nett + $this->calculator->employee->earnings->holidayAllowance + $this->calculator->employee->bonus->getSum()) - $this->result->ptkp->amount;
                $this->result->liability->annual = $this->result->pkp - $earningTax;
            } else {
                $this->result->pkp = $this->calculator->result->earnings->annualy->nett - $this->result->ptkp->amount;
                $this->result->liability->annual = $this->result->pkp * ($this->getRate($this->calculator->result->earnings->nett) / 100);
            }
            
            if($this->result->liability->annual > 0) {
                // Jika tidak memiliki NPWP dikenakan tambahan 20%
                if($this->calculator->employee->hasNPWP === false) {
                    $this->result->liability->annual = $this->result->liability->annual + ($this->result->liability->annual * (20/100));
                }

                $this->result->liability->monthly = $this->result->liability->annual / 12;
            } else {
                $this->result->liability->annual = 0;
                $this->result->liability->monthly = 0;
            }
        }
        
        return $this->result;
    }
}
