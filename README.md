# Indonesia Payroll Calculator
[![Latest Stable Version](https://poser.pugx.org/steevenz/id-payroll-calculator/v/stable)](https://packagist.org/packages/steevenz/id-payroll-calculator) [![Total Downloads](https://poser.pugx.org/steevenz/id-payroll-calculator/downloads)](https://packagist.org/packages/steevenz/id-payroll-calculator) [![Latest Unstable Version](https://poser.pugx.org/steevenz/id-payroll-calculator/v/unstable)](https://packagist.org/packages/steevenz/id-payroll-calculator) [![License](https://poser.pugx.org/steevenz/id-payroll-calculator/license)](https://packagist.org/packages/steevenz/id-payroll-calculator)

Ini merupakan PHP Component pembantu proses perhitungan gaji yang disesuaikan dengan peraturan-peraturan yang berlaku di Indonesia.

Referensi
---------
* [PPh21](https://www.online-pajak.com/perhitungan-pph-21)
* [PTKP](https://www.online-pajak.com/ptkp-terbaru-pph-21)
* [BPJS Kesehatan](https://www.panduanbpjs.com/iuran-bpjs-perbulan/)
* [BPJS Ketenagakerjaan](https://www.finansialku.com/berapa-iuran-bpjs-ketenagakerjaan-yang-harus-saya-bayar-dan-yang-ditanggung-perusahaan/)
* [UMP 2019](https://smartlegal.id/smarticle/layanan/2018/12/12/ini-daftar-upah-minimum-provinsi-ump-2019/)

Instalasi
---------
Cara terbaik untuk melakukan instalasi library ini adalah dengan menggunakan [Composer][7]
```
composer require steevenz/id-payroll-calculator
```

Penggunaan
----------
```php
use Steevenz\IndonesiaPayrollCalculator;

// Inisiasi class PayrollCalculator
$payrollCalculator = new PayrollCalculator();

// Set data karyawan
$payrollCalculator->employee->maritalStatus = true; // Menikah (true), Tidak Menikah/Single (false), secara default sudah terisi nilai false.
$payrollCalculator->employee->hasNPWP = true; // Secara default sudah terisi nilai true. Jika tidak memiliki npwp akan dikenakan potongan tambahan 20%
$payrollCalculator->employee->numOfDependentsFamily = 0; // Jumlah tanggungan, max 5 jika lebih akan dikenakan tambahannya perorang sesuai ketentuan BPJS Kesehatan

// Set data pendapatan default karyawan
$payrollCalculator->employee->earnings->basePay = 8000000; // Besaran nilai gaji pokok/bulan
$payrollCalculator->employee->earnings->overtime = 10000; // Besaran nilai uang lembur/jam
// NOTE: besaran nilai diatas bukan nilai hasil proses perhitungan absensi tetapi nilai default sebagai faktor perhitungan gaji.

// Set data kehadiran karyawan
$payrollCalculator->employee->presences->workDays = 25; // jumlah hari masuk kerja
$payrollCalculator->employee->presences->overtime = 0; //  perhitungan jumlah lembur dalam satuan jam
$payrollCalculator->employee->presences->latetime = 0; //  perhitungan jumlah keterlambatan dalam satuan jam
$payrollCalculator->employee->presences->travelDays = 0; //  perhitungan jumlah hari kepergian dinas
$payrollCalculator->employee->presences->indisposedDays = 0; //  perhitungan jumlah hari sakit yang telah memiliki surat dokter
$payrollCalculator->employee->presences->absentDays = 0; //  perhitungan jumlah hari alpha

// Set data tunjangan karyawan diluar tunjangan BPJS Kesehatan dan Ketenagakerjaan
$payrollCalculator->employee->allowances->offsetSet('tunjanganMakan', 100000);
// NOTE: Jumlah allowances tidak ada batasan

// Set data tunjangan karyawan diluar potongan BPJS Kesehatan dan Ketenagakerjaan
$payrollCalculator->employee->deductions->offsetSet('kasbon', 100000);
// NOTE: Jumlah deductions tidak ada batasan

// Set data ketentuan perusahaan
$payrollCalculator->provisions->company->numOfWorkingDays = 25; // Jumlah hari kerja dalam satu bulan
$payrollCalculator->provisions->company->calculateBPJSKesehatan = true; // Apakah perusahaan menyediakan BPJS Kesehatan / tidak untuk orang tersebut
$payrollCalculator->provisions->company->calculateBPJSKetenagakerjaan = true; // Apakah perusahaan menyediakan BPJS Ketenagakerjaan / tidak untuk orang tersebut
$payrollCalculator->provisions->company->riskGrade = 2; // Golongan resiko ketenagakerjaan, umumnya 2
$payrollCalculator->provisions->company->absentPenalty = 55000; // Perhitungan nilai potongan gaji/hari sebagai penalty.
$payrollCalculator->provisions->company->latetimePenalty = 100000; // Perhitungan nilai keterlambatan sebagai penalty.

// Set data ketentuan negara
$payrollCalculator->provisions->state->provinceMinimumWage = 3940972; // Ketentuan UMP sesuai propinsi lokasi perusahaan

// Mengambil hasil perhitungan
$payrollCalculator->getCalculation(); // Berupa array yang berisi seluruh data perhitungan gaji, lengkap dengan perhitungan BPJS dan PPh21
```

Untuk keterangan lebih lengkap dapat dibaca di [Wiki](https://github.com/steevenz/id-payroll-calculator/wiki)

Ide, Kritik dan Saran
---------------------
Jika anda memiliki ide, kritik ataupun saran, anda dapat mengirimkan email ke [steevenz@stevenz.com][3]. 
Anda juga dapat mengunjungi situs pribadi saya di [steevenz.com][1]

Bugs and Issues
---------------
Jika anda menemukan bugs atau issue, anda dapat mempostingnya di [Github Issues][6].

Requirements
------------
- PHP 7.2+
- [Composer][9]
- [O2System Spl][10]

[1]: http://steevenz.com
[2]: http://steevenz.com/blog/id-payroll-calculator-api
[3]: mailto:steevenz@steevenz.com
[4]: http://github.com/steevenz/id-payroll-calculator
[5]: http://github.com/steevenz/id-payroll-calculator/wiki
[6]: http://github.com/steevenz/id-payroll-calculator/issues
[7]: https://packagist.org/packages/steevenz/id-payroll-calculator
[9]: https://getcomposer.org
[10]: http://github.com/o2system/spl
