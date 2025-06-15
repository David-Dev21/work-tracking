<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Area;
use App\Models\Project;
use App\Models\Responsible;
use App\Models\AreaRole;
use App\Models\InternRegistration;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Intern;
use Carbon\Carbon;

class ActivityReportController extends Controller
{
    public function generateReport(Request $request)
    {
        try {
            Carbon::setLocale('es');
            $request->validate([
                'months' => 'required|string',
                'year' => 'required|string',
            ]);

            // Get the selected month and year
            $month = $request->months;
            $year = $request->year;            // Get the authenticated user ID
            $userId = Auth::id();

            // Verify user is authenticated
            if (!$userId) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            // Check if the user is an intern
            $intern = Intern::where('user_id', $userId)->first();
            if ($intern) {
                // Get activities for this intern
                $activitiesQuery = Activity::whereHas('assignments', function ($query) use ($intern) {
                    $query->where('intern_id', $intern->id);
                });

                // Get projects assigned to this intern
                $projectIds = $intern->assignments()
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->unique()
                    ->toArray();

                // Add activities from assigned projects
                if (!empty($projectIds)) {
                    $activitiesQuery->orWhereIn('project_id', $projectIds);
                }
            } else {
                // Get all activities for non-intern users
                $activitiesQuery = Activity::query();
            } // Filter by month and year
            $activitiesQuery->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);

            // Get the activities
            $activities = $activitiesQuery->with(['project', 'area'])->get();
            // Format month name for the report title
            $monthNames = [
                '01' => 'Enero',
                '02' => 'Febrero',
                '03' => 'Marzo',
                '04' => 'Abril',
                '05' => 'Mayo',
                '06' => 'Junio',
                '07' => 'Julio',
                '08' => 'Agosto',
                '09' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ];
            $selectedMonthName = $monthNames[$month] ?? $month;            // Generate a report ID
            $reportId = $this->generateReportId($year, $intern ?? null);            // Get intern details if user is an intern
            $internDetails = null;
            $areaName = null;
            $responsibleInfo = null;
            if ($intern) {
                $internDetails = [
                    'name' => $intern->name . ' ' . $intern->last_name,
                    'identity_card' => $intern->identity_card,
                    'university_registration' => $intern->university_registration,
                    'initials' => $this->getInternInitials($intern->name . ' ' . $intern->last_name)
                ];

                // Get area information from intern registrations
                $internRegistration = $intern->internRegistrations()
                    ->with(['area.areaRoles.responsibles'])
                    ->latest()
                    ->first();

                if ($internRegistration && $internRegistration->area) {
                    $areaName = $internRegistration->area->name;

                    // Get responsible from area roles
                    $areaRole = $internRegistration->area->areaRoles()->first();
                    if ($areaRole) {
                        $responsible = $areaRole->responsibles()->first();
                        if ($responsible) {
                            $responsibleInfo = [
                                'academic_degree' => $responsible->academic_degree ?? 'Lic.',
                                'name' => $responsible->name . ' ' . $responsible->last_name
                            ];
                        }
                    }
                }
            }

            // Set default values if no assignment found
            if (!$areaName) {
                $areaName = 'DESARROLLO DE SOFTWARE';
            }
            if (!$responsibleInfo) {
                $responsibleInfo = [
                    'academic_degree' => 'Lic.',
                    'name' => 'Wilson René Gonzales Sanchez'
                ];
            }

            // Create the PDF with the activities data - use the UPEA template
            $pdf = PDF::loadView('reports.activities-upea', [
                'activities' => $activities,
                'month' => $selectedMonthName,
                'year' => $year,
                'reportId' => $reportId,
                'internDetails' => $internDetails,
                'areaName' => $areaName,
                'responsibleInfo' => $responsibleInfo,
            ]);

            // Set PDF options - portrait for the UPEA format
            $pdf->setPaper('letter', 'portrait');

            // Enable PHP script execution for page numbering - CRÍTICO
            $dompdf = $pdf->getDomPDF();
            $options = $dompdf->getOptions();
            $options->setIsPhpEnabled(true);
            $dompdf->setOptions($options);        // Stream the PDF to the browser
            return response($pdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="informe-pasantia-' . $year . '.pdf"');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generando el reporte: ' . $e->getMessage()], 500);
        }
    }

    private function getInternInitials(string $fullName): string
    {
        $nameParts = explode(' ', $fullName);
        $initials = '';

        // Get the first letter of each part of the name (up to 4 parts)
        foreach ($nameParts as $index => $part) {
            if ($index < 4 && !empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }

        return $initials;
    }

    private function generateReportId(string $year, $intern = null): string
    {
        // Get the current month as a number (1-12)
        $month = now()->month;
        $initials = '';

        if ($intern) {
            $initials = $this->getInternInitials($intern->name . ' ' . $intern->last_name);
        }

        // Create the report ID using the month and year
        $reportId = "I.P. / U.T.I.C. " . $initials . " N° " . str_pad($month, 2, '0', STR_PAD_LEFT) . "/" . $year;

        return $reportId;
    }
}
