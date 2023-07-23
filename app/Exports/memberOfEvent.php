<?php

namespace App\Exports;

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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class memberOfEvent implements FromCollection, WithMapping, WithProperties, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles
{
    public $link;
    public $member;
    public $rows = 0;
    public $result;
    public $breakLine;

    /**
     * @var Link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->breakLine = chr(10);
    }

    public function properties(): array
    {
        return [
            'creator'        => config('app.name'),
            'title'          => 'Rekap Participant Event '. $this->link->title,
            'description'    => 'Export rekap participant event '. $this->link->title .'',
            'subject'        => 'Rekam Particapant Event',
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
            'Domisili',
            'Instansi',
            'Mendaftar Pada',
        ];
    }

    public function map($member): array
    {
        $this->rows++;

        return [
            $this->rows,
            $member->full_name,
            $member->email,
            $member->contact_number,
            $member->domisili == null || $member->domisili == '' ? 'NaN' : $member->domisili,
            $member->corporation,
            date('d-m-Y H:i:s', strtotime($member->created_at)),
        ];
    }

    public function columnWidths(): array
    {
        // all columns width = 55
        return [
            'B' => 55,
            'C' => 30, // email
            'D' => 30, // no hp
            'E' => 30, // domisili
            'F' => 30, // instansi
            'G' => 30, // mendaftar pada
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold tex, with a light blue background
            1    => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFA0A0A0']]],
        ];
    }

    public function getFilename()
    {
        return 'Rekap Peserta Event '. $this->link->title .'.xlsx';
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
        $this->getMemberOfEvent()
            ->filterMemberBasedOnLinkType();

        // sort lastest
        $this->member = $this->member->sortByDesc('id');

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
