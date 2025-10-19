<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #366092;
            padding-bottom: 20px;
        }
        
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #366092;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 10px;
            color: #666;
        }
        
        .export-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #366092;
        }
        
        .export-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .export-info p {
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: #366092;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:nth-child(odd) {
            background-color: white;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
            padding: 10px 20px;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($branding['logo_path']) && file_exists(public_path($branding['logo_path'])))
            <img src="{{ public_path($branding['logo_path']) }}" alt="Logo" class="logo">
        @endif
        
        <div class="company-name">{{ $branding['company_name'] ?? 'CRM System' }}</div>
        
        @if(!empty($branding['company_address']) || !empty($branding['company_phone']) || !empty($branding['company_email']))
            <div class="company-details">
                @if(!empty($branding['company_address']))
                    {{ $branding['company_address'] }}<br>
                @endif
                @if(!empty($branding['company_phone']))
                    Tel: {{ $branding['company_phone'] }}<br>
                @endif
                @if(!empty($branding['company_email']))
                    Email: {{ $branding['company_email'] }}
                @endif
            </div>
        @endif
    </div>
    
    <div class="export-title">{{ $options['title'] ?? 'Data Export' }}</div>
    
    <div class="export-info">
        <p><strong>Export Date:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
        <p><strong>Total Records:</strong> {{ count($data) }}</p>
        @if(!empty($options['description']))
            <p><strong>Description:</strong> {{ $options['description'] }}</p>
        @endif
    </div>
    
    @if(!empty($data))
        <table>
            <thead>
                <tr>
                    @if(!empty($headers))
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    @else
                        @foreach(array_keys($data[0] ?? []) as $key)
                            <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $row)
                    @if($index > 0 && $index % 50 == 0)
                        <tr class="page-break"></tr>
                    @endif
                    <tr>
                        @if(!empty($headers))
                            @foreach($headers as $header)
                                <td>{{ $row[$header] ?? '' }}</td>
                            @endforeach
                        @else
                            @foreach($row as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center">
            <p>No data available for export.</p>
        </div>
    @endif
    
    <div class="footer">
        <div class="text-center">
            Generated on {{ now()->format('Y-m-d H:i:s') }} | Page <span class="page-number"></span>
        </div>
    </div>
</body>
</html>
