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
 * @package Steevenz\IndonesiaPayrollCalculator\Taxes
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

            // print_r( $this->result->ptkp->amount ); die;

            // Annual PKP (Pajak Atas Upah)
            if($this->calculator->employee->earnings->holidayAllowance > 0 && $this->calculator->employee->bonus->getSum() == 0) {
                // print_r('1');
                // Pajak Atas Upah
                $earningTax = ($this->calculator->result->earnings->annualy->nett - $this->result->ptkp->amount) * ($this->getRate($this->calculator->result->earnings->nett) / 100);

                // Penghasilan + THR Kena Pajak
                $this->result->pkp = ($this->calculator->result->earnings->annualy->nett + $this->calculator->employee->earnings->holidayAllowance) - $this->result->ptkp->amount;

                $this->result->liability->annual = $this->result->pkp - $earningTax;
            } elseif($this->calculator->employee->earnings->holidayAllowance > 0 && $this->calculator->employee->bonus->getSum() > 0) {
                // print_r('2');
                // Pajak Atas Upah
                $earningTax = ($this->calculator->result->earnings->annualy->nett - $this->result->ptkp->amount) * ($this->getRate($this->calculator->result->earnings->nett) / 100);

                // Penghasilan + THR Kena Pajak
                $this->result->pkp = ($this->calculator->result->earnings->annualy->nett + $this->calculator->employee->earnings->holidayAllowance + $this->calculator->employee->bonus->getSum()) - $this->result->ptkp->amount;
                $this->result->liability->annual = $this->result->pkp - $earningTax;
            } else {
                $this->result->pkp = $this->calculator->result->earnings->annualy->nett - $this->result->ptkp->amount;
                // hasil = 230506740 - 67500000

                // print_r($this->result->pkp); die;
                // 139.375.121 
                // 60.000.000 * 5%
                // 79.375.121 * 15%

                // pkp 
                

                // print_r( $this->calculator->result->earnings->annualy->nett ); die;

                // $this->result->liability->annual = $this->result->pkp * ($this->getRate($this->calculator->result->earnings->annualy->nett) / 100);
                $potongan = 0;
                $loop = $this->getProgresive($this->calculator->result->earnings->annualy->nett);
                

                for($i = 0; $i < $loop; $i++){

                    if( $i == 0){

                        if($this->calculator->result->earnings->annualy->nett > 60000000){
                            $potongan = 60000000 * ( 5 / 100 );
                        } else {
                            $potongan = $this->result->pkp * ( 5 / 100 );
                        }
                        

                    } elseif($i == 1) {
                        
                        if($this->calculator->result->earnings->annualy->nett > 60000000 and $this->calculator->result->earnings->annualy->nett <= 250000000){
                            $potongan += ( $this->result->pkp - 60000000 ) * ( 15 / 100 );
                        } else {
                            $potongan += 190000000  * ( 15 / 100 );
                        }

                    } elseif($i == 2) {

                        if($this->calculator->result->earnings->annualy->nett > 250000000 and $this->calculator->result->earnings->annualy->nett <= 500000000){
                            $potongan += ( $this->result->pkp - 250000000 ) * ( 25 / 100 );
                        } else {
                            $potongan += 250000000  * ( 25 / 100 );
                        }

                    } elseif($i == 3) {

                        if($this->calculator->result->earnings->annualy->nett > 500000000 and $this->calculator->result->earnings->annualy->nett <= 5000000000){
                            $potongan += ( $this->result->pkp - 500000000 ) * ( 30 / 100 );
                        } else {
                            $potongan += 4500000000  * ( 30 / 100 );
                        }

                    } elseif($i == 4) {
                         
                        $potongan += ( $this->result->pkp - 5000000000 ) * ( 35 / 100 );
                    }

                }

                $this->result->liability->annual = $potongan;

                // print_r( $this->getRate($this->calculator->result->earnings->annualy->nett) ); die;
                // hasil = 163.006.740 * 5%
                // progresif belum
            }
            // die;
            
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
