<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>School Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2, h3 {
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        img {
            margin: 4px;
            border: 1px solid #ccc;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>

<h2>School Details</h2>

<table>
    <tr>
        <th>School Name</th>
        <td>{{ $data['school_name'] }}</td>
    </tr>
    <tr>
        <th>Address</th>
        <td>{{ $data['school_address'] }}</td>
    </tr>
    <tr>
        <th>Location</th>
        <td>
            {{ $data['city'] }},
            {{ $data['district'] }},
            {{ $data['state'] }}
        </td>
    </tr>
    <tr>
        <th>Establishment Date</th>
        <td>{{ $data['establishment_date'] }}</td>
    </tr>
    <tr>
        <th>Contact Number</th>
        <td>{{ $data['contact_number'] }}</td>
    </tr>
</table>

<h3>Students</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Standard</th>
            <th>Gender</th>
            <th>Year</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data['students'] as $index => $student)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>

                <td class="text-center">
                    @if ($student['photo'])
                        <img src="file://{{ $student['photo'] }}" width="40" height="40">
                    @endif
                </td>

                <td>{{ $student['student_name'] }}</td>
                <td>{{ $student['standard'] }}</td>
                <td>{{ $student['gender'] }}</td>
                <td>{{ $student['year'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No students found</td>
            </tr>
        @endforelse
    </tbody>
</table>

@if (count($data['photos']))
    <h3>School Photos</h3><br>
    @foreach ($data['photos'] as $photo)
        <img src="file://{{ $photo }}" width="120" height="90">
    @endforeach
@endif

<div class="footer">
    Generated on {{ now()->format('Y-m-d H:i:s') }}
</div>

</body>
</html>
