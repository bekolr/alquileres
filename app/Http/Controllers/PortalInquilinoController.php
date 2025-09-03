
<?php
class PortalInquilinoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // si usas roles:
        // $this->middleware('can:ver-portal-inquilino');
    }

    public function index()
    {
        $user = auth()->user();
        $inquilino = Inquilino::where('user_id',$user->id)->firstOrFail();

        $contrato = Contrato::with(['cuotas'=>fn($q)=>$q->orderBy('periodo','desc')])
            ->where('inquilino_id',$inquilino->id)
            ->where('estado','activo')
            ->first();

        return view('portal_inquilino.index', compact('inquilino','contrato'));
    }
}