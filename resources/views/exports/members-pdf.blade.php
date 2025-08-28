<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Membership Export - {{ now()->format('Y-m-d') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header .subtitle {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            .header {
                position: fixed;
                top: -60px;
                left: 0;
                right: 0;
                background-color: white;
                height: 60px;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Membership Register</h1>
        <div class="subtitle">Generated on: {{ now()->format('F j, Y H:i') }}</div>
    </div>

    @if(!empty($headers))
    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    @php
                        // Convert field names to human-readable format
                        $readableHeader = str_replace('_', ' ', $header);
                        $readableHeader = ucwords($readableHeader);
                        
                        // Special cases for specific fields
                        $headerMap = [
                            'id_number' => 'ID Number',
                            'ncpwd_number' => 'NCPWD Number',
                            'created_at' => 'Registration Date',
                            'other_names' => 'Other Names'
                        ];
                        
                        $displayHeader = $headerMap[$header] ?? $readableHeader;
                    @endphp
                    <th>{{ $displayHeader }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr>
                    @foreach($fields as $field)
                        @php
                            $value = $row[$field] ?? '';
                            
                            // Format specific fields
                            if ($field === 'created_at' && $value) {
                                $value = \Carbon\Carbon::parse($value)->format('M j, Y');
                            }
                            if ($field === 'date_of_birth' && $value) {
                                $value = \Carbon\Carbon::parse($value)->format('M j, Y');
                            }
                            if ($field === 'gender' && $value) {
                                $value = ucfirst($value);
                            }
                            if ($field === 'disability_status' && $value) {
                                $value = ucfirst($value);
                            }
                        @endphp
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
                
                {{-- Add page break every 25 rows to avoid cutting rows --}}
                @if(($index + 1) % 25 === 0 && !$loop->last)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table>
                    <thead>
                        <tr>
                            @foreach($headers as $header)
                                @php
                                    $readableHeader = str_replace('_', ' ', $header);
                                    $readableHeader = ucwords($readableHeader);
                                    $headerMap = [
                                        'id_number' => 'ID Number',
                                        'ncpwd_number' => 'NCPWD Number',
                                        'created_at' => 'Registration Date',
                                        'other_names' => 'Other Names'
                                    ];
                                    $displayHeader = $headerMap[$header] ?? $readableHeader;
                                @endphp
                                <th>{{ $displayHeader }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                @endif
            @endforeach
        </tbody>
    </table>
    @else
    <table>
        <tbody>
            @foreach($data as $index => $row)
                <tr>
                    @foreach($fields as $field)
                        @php
                            $value = $row[$field] ?? '';
                            
                            // Format specific fields
                            if ($field === 'created_at' && $value) {
                                $value = \Carbon\Carbon::parse($value)->format('M j, Y');
                            }
                            if ($field === 'date_of_birth' && $value) {
                                $value = \Carbon\Carbon::parse($value)->format('M j, Y');
                            }
                            if ($field === 'gender' && $value) {
                                $value = ucfirst($value);
                            }
                            if ($field === 'disability_status' && $value) {
                                $value = ucfirst($value);
                            }
                        @endphp
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
                
                {{-- Add page break every 25 rows --}}
                @if(($index + 1) % 25 === 0 && !$loop->last)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table>
                    <tbody>
                @endif
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Page 1 of {{ ceil(count($data) / 25) }} • Total Records: {{ count($data) }}
    </div>
</body>
</html>