<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\MemberAttend;
use App\Models\Link;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class attendanceOfEvent implements FromCollection, WithMapping, WithProperties, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    public $link;
    public $attendance;
    public $member;
    public $rows = 0;
    public $result;
    public $breakLine;

    /**
     * @var Attendance $attendance
     */
    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
        $this->breakLine = chr(10);
        $this->member = $attendance->member_attend;
        $link = $attendance->link;
        $this->link = $link;
        $members = $link->members;
        $members->map(function ($item, $key){
            // $item->attending = false;
            // if not in member, add attending false
            if ($this->member->contains('id', $item->id)) {
                $item->attending = false;
                $item->certificate = false;
                $item->payment_proof = null;
                $item->attend_at = null;
            } else {
                $item->certificate = $this->member->where('member_id', $item->id)->first()->certificate;
                $item->payment_proof = $this->member->where('member_id', $item->id)->first()->payment_proof;
                $item->attending = true;
                $item->attend_at = $this->member->where('member_id', $item->id)->first()->created_at;
            }
        });

        $this->member = $members;
    }

    public function map($member): array
    {
        $this->rows++;

        return [
            $this->rows,
            $member->full_name,
            $member->email,
            $member->contact_number,
            $member->corporation,
            $member->certificate ? 'Ya' : 'Tidak',
            $member->payment_proof ? 'Sudah' : 'Belum',
            $member->attend_at ? date('d-m-Y H:i:s', strtotime($member->attend_at)) : 'Belum',
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => config('app.name'),
            'title'          => 'Rekap Absensi Event '. $this->link->title,
            'description'    => 'Export rekap absensi event '. $this->link->title .'',
            'subject'        => 'Rekam Absensi Event',
            'company'        => config('app.name'),
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Nama Lengkap',
            'Email',
            'No. HP',
            'Instansi',
            'Perlu Sertifikat ?', // certificate
            'Bukti Pembayaran', // payment_proof
            'Absensi Pada'
        ];
    }

    public function columnWidths(): array
    {
        // all columns width = 55
        return [
            'B' => 55,
            'C' => 30, // email
            'D' => 20, // no hp
            'E' => 30, // instansi
            'F' => 10, // certificate
            'G' => 30, // payment_proof
            'H' => 30, // attending
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowStyle = [
            1   => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFA0A0A0']]],
        ];

        return $rowStyle;
    }

    public function registerEvents(): array
    {
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $countRow = $this->member->count();
        $cellRange = 'A2:H'.($countRow + 1);
        $certificateRange = 'F2:F'.($countRow + 1);
        $afterSheet = function(AfterSheet $event) use ($cellRange, $styleArray, $certificateRange) {
            $sheet = $event->sheet->getDelegate();
            $conditional1 = new Conditional();
            $conditional2 = new Conditional();
            $conditional1->setConditionType(Conditional::CONDITION_CELLIS)
                ->setOperatorType(Conditional::OPERATOR_EQUAL)
                ->addCondition('"Ya"')
                ->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getEndColor()->setARGB('00FF00');
            $conditional2->setConditionType(Conditional::CONDITION_CELLIS)
                ->setOperatorType(Conditional::OPERATOR_EQUAL)
                ->addCondition('"Tidak"')
                ->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getEndColor()->setARGB('FF0000');
            $sheet->getStyle($certificateRange)->setConditionalStyles([$conditional1, $conditional2]);
            $sheet->getStyle($cellRange)->applyFromArray($styleArray);

            return $sheet;
        };
        
        return [
            AfterSheet::class => $afterSheet,
        ];
    }

    public function getFilename()
    {
        return 'Rekap Absensi Peserta Event '. $this->link->title .'.xlsx';
    }

    public function getMemberOfEvent()
    {
        $this->member = $this->link->members;

        return $this;
    }

    public function filterMemberBasedOnLinkType()
    {
        if ($this->link->link_type == 'pay') {
            $this->member = $this->member->filter(function ($member) {
                return $member->invoices->status == 2;
            });
        }

        return $this;
    }

    public function exportProcess()
    {
        // sort member by attend_at desc
        $this->member = $this->member->sortByDesc('attend_at');
        $this->result = $this->member;

        return $this;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->result;
    }
}
