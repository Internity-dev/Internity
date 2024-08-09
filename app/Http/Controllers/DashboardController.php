<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Monitor;
use App\Models\Vacancy;
use App\Models\InternDate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $isManager = $user->hasRole('manager');
        $isKaprog = $user->hasRole('kepala program');
        $isMentor = $user->hasRole('mentor');
        $isTeacher = $user->hasRole('teacher');

        if ($isKaprog) {
            $departmentId = $user->departments()->first()->id;
            $students = User::kaprog($departmentId)->count();
            $companies = Company::where('department_id', $departmentId)->count();
            $vacancies = Vacancy::whereHas('company', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->count();

            $studentStatus = [
                'finished' => User::whereRelation('departments', 'id', $departmentId)
                                ->whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('departments', function ($query) use ($departmentId) {
                                    $query->where('id', $departmentId);
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', true);
                                })->count(),

                'intern' => User::whereRelation('departments', 'id', $departmentId)
                                ->whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('departments', function ($query) use ($departmentId) {
                                    $query->where('id', $departmentId);
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', false);
                                })->count(),
                'not_intern' => User::whereRelation('departments', 'id', $departmentId)
                                ->whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('departments', function ($query) use ($departmentId) {
                                    $query->where('id', $departmentId);
                                })
                                ->whereDoesntHave('internDates')->count(),
            ];

            // Get count of each match column value (1/2/3/4)
            $monitors = Monitor::whereHas('company', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->get()->groupBy('match')->map(function ($item) {
                return $item->count();
            });

            // map keys into "{keys} bulan"
            $internDurations = InternDate::whereHas('company', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->get()->groupBy('duration')->map(function ($item) {
                return $item->count();
            })->mapWithKeys(function ($item, $key) {
                return [$key . ' bulan' => $item];
            });
        } elseif ($isMentor) {
            $companyId = $user->companies()->first()->id;

            $students = User::mentor($companyId)->count();
            $companies = Company::where('id', $companyId)->count();
            $vacancies = Vacancy::where('company_id', $companyId)->count();

            $studentStatus = [
                'finished' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('companies', function ($query) use ($companyId) {
                                    $query->where('id', $companyId);
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', true);
                                })->count(),

                'intern' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('companies', function ($query) use ($companyId) {
                                    $query->where('id', $companyId);
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', false);
                                })->count(),
                'not_intern' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('companies', function ($query) use ($companyId) {
                                    $query->where('id', $companyId);
                                })
                                ->whereDoesntHave('internDates')->count(),
            ];

            // Get count of each match column value (1/2/3/4)
            $monitors = Monitor::where('company_id', $companyId)->get()->groupBy('match')->map(function ($item) {
                return $item->count();
            });

            // map keys into "{keys} bulan"
            $internDurations = InternDate::where('company_id', $companyId)->get()->groupBy('duration')->map(function ($item) {
                return $item->count();
            })->mapWithKeys(function ($item, $key) {
                return [$key . ' bulan' => $item];
            });
        } elseif ($isManager) {
            $schoolId = $user->schools()->first()->id;
            $students = User::manager($schoolId)->count();
            $companies = Company::whereHas('department', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })->count();
            $vacancies = Vacancy::whereHas('company', function ($query) use ($schoolId) {
                $query->whereHas('department', function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                });
            })->count();

            $studentStatus = [
                'finished' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('schools', function ($query) use ($schoolId) {
                                    $query->where('id', $schoolId);
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', true);
                                })->count(),

                'intern' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('schools', function ($query) use ($schoolId) {
                                    $query->where('id', $schoolId);
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', false);
                                })->count(),
                'not_intern' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('schools', function ($query) use ($schoolId) {
                                    $query->where('id', $schoolId);
                                })
                                ->whereDoesntHave('internDates')->count(),
            ];

            // Get count of each match column value (1/2/3/4)
            $monitors = Monitor::whereHas('company', function ($query) use ($schoolId) {
                $query->whereHas('department', function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                });
            })->get()->groupBy('match')->map(function ($item) {
                return $item->count();
            });

            // map keys into "{keys} bulan"
            $internDurations = InternDate::whereHas('company', function ($query) use ($schoolId) {
                $query->whereHas('department', function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                });
            })->get()->groupBy('duration')->map(function ($item) {
                return $item->count();
            })->mapWithKeys(function ($item, $key) {
                return [$key . ' bulan' => $item];
            });
        } elseif ($isTeacher) {
            $students = User::teacher()->count();
            $companies = Company::count();
            $vacancies = Vacancy::count();

            $studentStatus = [
                'finished' => User::teacher()->whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', true);
                                })->count(),

                'intern' => User::teacher()->whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', false);
                                })->count(),
                'not_intern' => User::teacher()->whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereDoesntHave('internDates')->count(),
            ];

            // Get count of each match column value (1/2/3/4)
            $monitors = Monitor::all()->groupBy('match')->map(function ($item) {
                return $item->count();
            });

            // map keys into "{keys} bulan"
            $internDurations = InternDate::all()->groupBy('duration')->map(function ($item) {
                return $item->count();
            })->mapWithKeys(function ($item, $key) {
                return [$key . ' bulan' => $item];
            });
        } else {
            $students = User::whereHas('roles', function ($query) {
                $query->where('name', 'student');
            })->count();
            $companies = Company::count();
            $vacancies = Vacancy::count();

            $studentStatus = [
                'finished' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', true);
                                })->count(),

                'intern' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereHas('internDates', function ($query) {
                                    $query->where('finished', false);
                                })->count(),
                'not_intern' => User::whereHas('roles', function ($query) {
                                    $query->where('name', 'student');
                                })
                                ->whereDoesntHave('internDates')->count(),
            ];

            // Get count of each match column value (1/2/3/4)
            $monitors = Monitor::all()->groupBy('match')->map(function ($item) {
                return $item->count();
            });

            // map keys into "{keys} bulan"
            $internDurations = InternDate::all()->groupBy('duration')->map(function ($item) {
                return $item->count();
            })->mapWithKeys(function ($item, $key) {
                return [$key . ' bulan' => $item];
            });
        }

        // Sort array by key
        $internDurations = $internDurations->sortKeys();
        return view('dashboard', compact('user','students', 'companies', 'vacancies', 'studentStatus', 'monitors', 'internDurations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
