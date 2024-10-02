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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class memberOfEvent implements FromCollection, WithMapping, WithProperties, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles
{
    public $link;
    public $member;
    public $rows = 0;
    public $result;
    public $breakLine;
    public $linkMultiRegist = false;
    public $multiRegistCount = 0;

    /**
     * @var Link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->breakLine = chr(10);
        $this->linkMultiRegist = $link->is_multiple_registrant_allowed;
        $this->multiRegistCount = $link->sub_member_limit;
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
        $head = [
            '#',
            'Nama Lengkap',
            'Email',
            'No. HP',
            'Domisili',
            'Instansi',
            'Invoice Code',
            'Mendaftar Pada',
        ];

        if ($this->linkMultiRegist) {
            for ($i=0; $i < $this->multiRegistCount ; $i++) { 
                $head[] = 'Nama Peserta '.($i + 1);
                $head[] = 'Nomor Telepon Peserta '.($i + 1);
            }
        } 

        return $head;
    }

    public function map($member): array
    {
        $this->rows++;
        $data = [];
        if ($this->linkMultiRegist) {
            $data = [
                $this->rows,
                $member->full_name,
                $member->email,
                $member->contact_number,
                $member->domisili == null || $member->domisili == '' ? 'NaN' : $member->domisili,
                $member->corporation,
                $member->invoices == null ? 'NaN' : ($member->invoices->token . ' - ' . ($member->invoices->is_automatic ? $member->invoices->payment_method : 'Manual')),
                date('d-m-Y H:i:s', strtotime($member->created_at)),
            ];
            foreach ($member->subMembers as $key => $sub_member) {
                $data[] = $sub_member->full_name;
                $data[] = $sub_member->contact_number;
            }
        } else {
            $data = [
                $this->rows,
                $member->full_name,
                $member->email,
                $member->contact_number,
                $member->domisili == null || $member->domisili == '' ? 'NaN' : $member->domisili,
                $member->corporation,
                $member->invoices == null ? 'NaN' : ($member->invoices->token . ' - ' . ($member->invoices->is_automatic ? $member->invoices->payment_method : 'Manual')),
                date('d-m-Y H:i:s', strtotime($member->created_at)),
            ];
        }

        return $data;
    }

    public function columnWidths(): array
    {
        $default_col = [
            'A' => 5, // #
            'B' => 55, // Nama Lengkap
            'C' => 30, // email
            'D' => 30, // no hp
            'E' => 30, // domisili
            'F' => 30, // instansi
            'G' => 30, // invoice code
            'H' => 30, // mendaftar pada
        ];
        $countDefault = count($default_col);

        if ($this->linkMultiRegist) {
            for ($i=0; $i < $this->multiRegistCount ; $i++) { 
                $startIndex = ($i * 2) + 1;
                $default_col[Coordinate::stringFromColumnIndex($countDefault + ($startIndex))] = 55; // Nama Peserta
                $default_col[Coordinate::stringFromColumnIndex($countDefault + ($startIndex + 1))] = 30; // Nomor Telepon Peserta
            }
        }

        return $default_col;
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
        $title = $this->link->title;
        // delete special character
        $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);

        return 'Rekap Peserta Event '. $title .'.xlsx';
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
