<?php
class ReporteAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('can:ver-panel-admin');
    }

    public function ingresos(Request $request)
    {
        $desde = $request->date('desde') ?? now()->startOfYear();
        $hasta = $request->date('hasta') ?? now()->endOfYear();

        $pagos = Pago::selectRaw('DATE_FORMAT(fecha_pago, "%Y-%m") as ym, SUM(importe) as total')
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        return view('admin.ingresos', compact('pagos','desde','hasta'));
    }
}
