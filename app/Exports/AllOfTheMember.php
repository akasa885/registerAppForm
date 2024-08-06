<?php

namespace App\Exports;

use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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

class AllOfTheMember implements FromCollection, WithMapping, WithProperties, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles
{
    public $result;
    public $rows = 0;
    public $member;

    public function properties(): array
    {
        return [
            'creator'        => config('app.name'),
            'title'          => 'List of All Member',
            'description'    => 'Export list of all member',
            'subject'        => 'List of All Member',
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
        ];

        return $head;
    }

    public function columnWidths(): array
    {
        $default_col = [
            'A' => 5, // #
            'B' => 55, // Nama Lengkap
            'C' => 30, // email
            'D' => 30, // no hp
            'E' => 30, // domisili
        ];

        return $default_col;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold tex, with a light blue background
            1    => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFA0A0A0']]],
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
        ];
    }

    public function exportProcess()
    {
        $data = Member::query()
            ->select(
                'email',
                DB::raw('count(*) as registered_count'),
                DB::raw('max(members.created_at) as last_registered')
            )
            ->join('links', 'members.link_id', '=', 'links.id')
            ->leftJoin('invoices', function ($join) {
                $join->on('members.id', '=', 'invoices.member_id')->where('invoices.status', 2);
            })
            ->where(function ($type) {
                $type->where('links.link_type', 'free')
                    ->orWhere(function ($query) {
                        $query->where('links.link_type', 'pay')->whereNotNull('invoices.id');
                    });
            });

        if (Gate::allows('isSuperAdmin')) {
            $data = $data->groupBy('email')
                ->orderBy('registered_count', 'desc')
                ->get();
        } else {

            $data = $data->where('links.created_by', auth()->user()->id)
                ->groupBy('email')
                ->orderBy('registered_count', 'desc')
                ->get();
        }

        // get the full_name & contact_number & domisili from member by data email

        $data->map(function ($item) {
            $member = Member::where('email', $item->email)->latest()->first();
            $item->full_name = $member->full_name;
            $item->contact_number = $member->contact_number;
            $item->domisili = $member->domisili;

            return $item;
        });

        $this->member = $data;

        $this->result = $data;

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
