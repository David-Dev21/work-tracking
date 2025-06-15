<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INFORME DE PASANTÍA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 15px;
            line-height: 1.5;
        }

        .header-image {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
            position: relative;
        }

        .header-image img {
            max-width: 100%;
            height: auto;
            border-bottom: 1px solid #ccc;
        }

        .header-title {
            text-align: center;
            margin: 15px auto;
            padding: 10px;
            border: 2px solid #1e3a8a;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            color: #1e3a8a;
            width: 90%;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-section {
            margin: 0 auto 20px;
            border-bottom: 1px solid #333;
            width: 90%;
            max-width: 650px;
        }

        .info-row {
            margin-bottom: 10px;
        }

        .info-row .label {
            display: inline-block;
            width: 60px;
            font-weight: bold;
            vertical-align: top;
        }

        .info-row .content {
            display: inline-block;
            width: calc(100% - 70px);
        }

        .section-title {
            font-weight: bold;
            margin: 25px auto 10px;
            padding-bottom: 5px;
            font-size: 14px;
            width: 90%;
            max-width: 650px;
        }

        .section-content {
            margin: 10px auto;
            text-align: justify;
            width: 90%;
            max-width: 650px;
        }

        table {
            width: 90%;
            max-width: 750px;
            border-collapse: collapse;
            margin: 15px auto;
        }

        table,
        th,
        td {
            border: 1px solid #1e3a8a;
        }

        th {
            background-color: #64cbde;
            padding: 6px;
            color: #000;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        td {
            padding: 6px;
            font-size: 13px;
            vertical-align: top;
        }

        .centered {
            text-align: center;
        }

        .conclusion {
            margin: 20px auto;
            text-align: justify;
            width: 90%;
            max-width: 650px;
        }

        .signature {
            margin: 100px auto 0;
            text-align: center;
            font-size: 13px;
            width: 90%;
            max-width: 650px;
        }

        .signature-line {
            display: block;
            width: 50%;
            margin: 0 auto;
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }

        .footer {
            margin: 30px auto 0;
            font-size: 8px;
            color: #666;
            position: absolute;
            bottom: 20px;
            width: 90%;
            max-width: 650px;
            text-align: left;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-left {
            flex: 1;
        }

        .footer-right {
            flex: 0 0 auto;
            text-align: right;
            font-weight: bold;
        }
    </style>
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->getFont("Arial");
            $size = 9;
            $color = array(0, 0, 0);
            $pdf->page_text(520, 780, $text, $font, $size, $color);
        }
    </script>
</head>

<body>
    <div class="header-image">
        <!-- Usar la ruta correcta para su logo -->
        <img src="{{ public_path('images/cabecera-pdf (2).png') }}" alt="Logo Universidad" height="70">
    </div>
    <div class="header-title">
        <span style="font-size: 18px;">INFORME DE PASANTÍA</span>
        @if (isset($reportId))
            <br>{{ $reportId }}
        @endif
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="label">A</div>
            <div class="content">
                <span>:
                    M. SC. ING. Juan Regis Munez Sirpa
                </span><br>
                <strong>
                    JEFE DE UNIDAD<br>
                    UNIDAD DE TECNOLOGÍAS DE INFORMACIÓN Y COMUNICACIONES - U.P.E.A.
                </strong>
            </div>
        </div>

        <div class="info-row">
            <div class="label">VÍA</div>
            <div class="content">
                <span>:
                    @if (isset($responsibleInfo) && isset($responsibleInfo['academic_degree']) && isset($responsibleInfo['name']))
                        {{ $responsibleInfo['academic_degree'] }} {{ $responsibleInfo['name'] }}
                    @else
                        Sin información del responsable
                    @endif
                </span><br>
                <strong>
                    ENCARGADO -
                    @if (isset($areaName))
                        ÁREA DE {{ strtoupper($areaName) }}
                    @else
                        Sin información del área
                    @endif
                    <br>
                    UNIDAD DE TECNOLOGÍAS DE INFORMACIÓN Y COMUNICACIONES - U.P.E.A.
                </strong>
            </div>
        </div>

        <div class="info-row">
            <div class="label">DE</div>
            <div class="content">
                <span>: Univ. {{ $internDetails['name'] }}</span><br>
                <strong>
                    PASANTE DEL
                    @if (isset($areaName))
                        ÁREA DE {{ strtoupper($areaName) }}
                    @else
                        ÁREA DE DESARROLLO DE SOFTWARE
                    @endif
                    <br>
                    UNIDAD DE TECNOLOGÍAS DE INFORMACIÓN Y COMUNICACIONES - U.P.E.A.
                </strong>
            </div>
        </div>

        <div class="info-row">
            <div class="label">REF</div>
            <div class="content">
                <strong>: INFORME DE ACTIVIDADES PRÁCTICA PROFESIONAL – PASANTÍA
                    MES DE {{ strtoupper($month) }} </strong>
            </div>
        </div>

        <div class="info-row">
            <div class="label">FECHA</div>
            <div class="content">
                <span>: El Alto, {{ now()->format('d') }} de {{ now()->locale('es')->monthName }} de
                    {{ $year }}</span>
            </div>
        </div>
    </div>

    <div class="section-title">1. ANTECEDENTES</div>
    <div class="section-content">
        De acuerdo con las directrices proporcionadas por su autoridad en {{ $month }} de
        {{ $year }}, y con
        el fin de realizar diversas actividades en la UNIDAD DE TECNOLOGÍAS DE
        INFORMACIÓN Y COMUNICACIONES - U.P.E.A., a continuación, se detalla la
        información relevante:
    </div>
    <div class="section-title">2. DESARROLLO</div>
    <div class="section-content">
        Las diversas actividades, llevadas a cabo bajo la supervisión del
        @if (isset($areaName))
            Área de {{ ucfirst(strtolower($areaName)) }}
        @else
            Área de Desarrollo de Software
        @endif
        en la Unidad De Tecnologías De Información Y Comunicaciones – U.P.E.A., se detallan
        a continuación. Siguiendo los términos de referencia, informo a su autoridad sobre las
        actividades realizadas durante el mes de {{ $month }}, entre las 14:00 a.m. y las 18:00 p.m.
    </div>
    <div class="section-content">
        En el cuadro siguiente se presenta el trabajo diario realizado, junto con las observaciones
        pertinentes para su consideración.
    </div>

    @if ($activities->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nro.</th>
                    <th>FECHA </th>
                    <th>ACTIVIDAD</th>
                    <th>SUPERVISOR</th>
                    <th>OBSERVACIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $index => $activity)
                    <tr>
                        <td style="width: 5%" class="centered">{{ $index + 1 }}</td>
                        <td style="width: 15%">
                            {{ \Carbon\Carbon::parse($activity->created_at)->locale('es')->translatedFormat('l') }}<br>
                            {{ $activity->created_at->format('d-m-Y') }}
                        </td>
                        <td style="width: 40%">
                            <strong>{{ $activity->name ?? $activity->title }}</strong><br>{{ $activity->description }}
                        </td>
                        <td style="width: 25%">
                            @if (isset($activity->responsible) && $activity->responsible)
                                {{ $activity->responsible->academic_degree ?? 'Ing.' }}
                                {{ $activity->responsible->name }} {{ $activity->responsible->last_name }}
                            @elseif(isset($responsibleInfo) && isset($responsibleInfo['academic_degree']) && isset($responsibleInfo['name']))
                                {{ $responsibleInfo['academic_degree'] }} {{ $responsibleInfo['name'] }}
                            @else
                                Lic. Wilson René Gonzales Sanchez
                            @endif
                        </td>
                        <td style="width: 15%"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; width: 90%; max-width: 650px; margin: 20px auto;">No se encontraron actividades
            para el período seleccionado.</p>
    @endif

    <div class="section-title">3. CONCLUSIONES</div>
    <div class="conclusion">
        El avance de las diversas actividades en la Unidad de Tecnologías de Información y
        Comunicaciones (U-TIC) – U.P.E.A. se llevó a cabo con algunos contratiempos, aunque
        en su mayoría conforme a lo previsto.
        <br><br>
        Sin más que añadir, me despido con el mayor respeto, deseándole éxito en las tareas
        que realiza en beneficio de nuestra universidad.
        <br><br>
        Atentamente.
    </div>
    <div class="signature">
        <span class="signature-line"></span>
        Univ. {{ $internDetails['name'] }}<br>
        @if (isset($internDetails))
            C.I: {{ $internDetails['identity_card'] }}<br>
            R.U: {{ $internDetails['university_registration'] }}<br>
        @else
            C.I: Sin Datos<br>
            R.U: Sin Datos<br>
        @endif
        <strong>
            PASANTE DE U-TIC<br>
            @if (isset($areaName))
                ÁREA {{ strtoupper($areaName) }}
            @else
                Sin Información del Área
            @endif
        </strong>
    </div>
    <div class="footer">
        <div class="footer-left">
            <small>
                JRMS /
                {{ $internDetails['initials'] }}
                <br>
                CC / Docente de la asignatura<br>
                CC / Unidad U-TIC
            </small>
        </div>
        <div class="footer-right">
            <small>
                @if (isset($reportId))
                    {{ $reportId }}
                @endif
            </small>
        </div>
    </div>
</body>

</html>
