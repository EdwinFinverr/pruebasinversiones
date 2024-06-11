<div class="col-12">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3" style="min-height: 75vh;">
        @forelse ($inversionesActivas as $inversion)
            @if ($inversion->estado_inversion_id !== 2 && $inversion->estado_inversion_id !== 3)
                <div class="col ">
                    <div class="card  {{ $inversion->estado_inversion_id == 4 || $inversion->estado_inversion_id == 5 ? 'border-danger' : 'border-secondary' }} my-3"
                        style="height: 70vh;">
                        <div class="card-header text-center">Folio:
                            P{{ $inversion->empresa_inversion_id == 1 ? 'C' : 'F' }} - {{ $inversion->folio }}</div>
                        <div class="card-body text-secondary text-center  d-flex flex-column justify-content-around">
                            <h2 class="card-title text-primary">{{ $inversion->cantidad }}</h2>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    Vigencia: <br> Desde
                                    {{ \Carbon\Carbon::parse($inversion->fecha_inicio)->format('d/m/Y') }}
                                </li>
                                <li
                                    class="list-group-item {{ $inversion->estado_inversion_id == 4 ? 'text-danger' : ' ' }}">
                                    Hasta {{ \Carbon\Carbon::parse($inversion->fecha_termino)->format('d/m/Y') }}</li>
                                <li class="list-group-item">
                                    @if ($inversion->contrato_inversion_id == 1)
                                        <a href="{{ route('customerControl.printpdf', [$inversion->id]) }}"
                                            target="_blank" class="btn btn-outline-secondary"
                                            style="border-style: none;"><img
                                                src="{{ asset('img/Recurso%20129siibal-.png') }}"
                                                style="background: top / contain no-repeat, rgba(13,110,253,0);width: 150px;height:
                                    50px;border-top-left-radius: 0px;border-top-right-radius: 0px;border-bottom-right-radius: 0px;border-bottom-left-radius:
                                    0px;border-style: none;"></a>
                                    @elseif ($inversion->contrato_inversion_id == 2 || 3 || 4)
                                        <a href="{{ route('reinversionControl.pdf', $inversion->id) }}" target="_blank"
                                            class="btn btn-outline-secondary" style="border-style: none;"><img
                                                src="{{ asset('img/Recurso%20129siibal-.png') }}"
                                                style="background: top / contain no-repeat, rgba(13,110,253,0);width: 150px;height:
                                50px;border-top-left-radius: 0px;border-top-right-radius: 0px;border-bottom-right-radius: 0px;border-bottom-left-radius:
                                0px;border-style: none;"></a>
                                    @endif
                                </li>
                                @if ($inversion->estado_inversion_id == 4)
                                    <li class="list-group-item">
                                        <a href="{{ route('reinversion', ['id' => $inversion->id]) }}"
                                            class="btn btn-lg btn-block btn-outline-success">Reinvertir</a>
                                        <button type="button" class="btn btn-lg btn-block btn-outline-secondary"
                                            data-toggle="modal" data-target="#modalRetirar">
                                            Retirar Inversión
                                        </button>
                                        <div class="modal fade" id="modalRetirar" tabindex="-1"
                                            aria-labelledby="modalRetirarLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalRetirarLabel">¡Sigue ganando
                                                            con Finverr!
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h3>¿Estas seguro qué quieres dejar de ganar con Finverr?</h3>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form action="{{ route('patchInversion', $inversion->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="btn btn-outline-secondary">Retirar
                                                                Inversion</button>
                                                        </form>
                                                        <a href="{{ route('reinversion', ['id' => $inversion->id]) }}"
                                                            class="btn btn-primary">Seguir ganando</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </li>
                                @elseif ($inversion->estado_inversion_id == 5)
                                    <li class="list-group-item">
                                        <form method="post" action="{{ route('postComprobante') }}"
                                            enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="{{ $inversion->id }}">
                                            </div>
                                            <button type="button" class="btn btn-lg btn-block btn-outline-secondary"
                                                data-toggle="modal" data-target="#modalTransferencia"
                                                style="border-style: none;">
                                                <img src="{{ asset('img/Recurso%20135siibal-.png') }}"
                                                    style="background: top / contain no-repeat, rgba(13,110,253,0);width: 260px;height:
                        50px;border-top-left-radius: 0px;border-top-right-radius: 0px;border-bottom-right-radius: 0px;border-bottom-left-radius:
                        0px;border-style: none;">
                                                <br></br>
                                            </button>
                                            <div class="form-group" style="overflow: hidden !important">
                                                <input type="file" name="comprobanteImg" required />
                                                <label for="fiscalphoto" style="color: red">Selecciona tu Comprobante de
                                                    pago</label>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-lg btn-block btn-outline-danger"
                                                    style="border-style: none;"><img
                                                        src="{{ asset('img/Recurso%20134siibal-.png') }}"
                                                        style="background: top / contain no-repeat, rgba(13,110,253,0);width: 260px;height:
                                    50px;border-top-left-radius: 0px;border-top-right-radius: 0px;border-bottom-right-radius: 0px;border-bottom-left-radius:
                                    0px;border-style: none;"></button>

                                                <div class="modal fade" id="modalTransferencia" tabindex="-1"
                                                    aria-labelledby="modalTransferenciaLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h3 class="modal-title" id="modalTransferenciaLabel">
                                                                    Realiza tu transferencia
                                                                </h3>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row my-2">
                                                                    <div class="col-12">

                                                                        <div class="table-responsive">
                                                                            <table class="table">
                                                                                <thead class="thead-dark">
                                                                                    <tr>
                                                                                        <th scope="col">Banco</th>
                                                                                        <th scope="col">No. de
                                                                                            Cuenta
                                                                                        </th>
                                                                                        <th scope="col">CLABE</th>
                                                                                        <th scope="col">Titular</th>
                                                                                        <th scope="col">Correo</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        @if ($inversion->empresa_inversion_id == 1)
                                                                                            <th scope="row">
                                                                                                Santander
                                                                                            </th>
                                                                                            <td>65-50738036-8</td>
                                                                                            <td>014010655073803681</td>
                                                                                            <td> CALFKA CAPITAL DISENO Y
                                                                                                CONSTRUCCION S DE RL DE
                                                                                                CV</td>
                                                                                            <td>inversiones@finverr.com
                                                                                            </td>
                                                                                        @else
                                                                                            <th scope="row">
                                                                                                Santander
                                                                                            </th>
                                                                                            <td>65-50604975-6</td>
                                                                                            <td>014010655060497563</td>
                                                                                            <td>FINVERR CORPORATIVO
                                                                                                GLOBAL SA de CV </td>
                                                                                            <td>inversiones@finverr.com
                                                                                            </td>
                                                                                        @endif

                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </li>
                                @else
                                    @switch($inversion->estado_inversion_id)
                                        @case(8)
                                            <li class="list-group-item text-primary">
                                                <p> Se recibió la solicitud de devolución, recibirás tu dinero 30 dias habiles
                                                    después de finalizar la inversión </p>
                                            </li>
                                        @break

                                        @case(6)
                                            <li class="list-group-item text-primary">
                                                <p> Se recibió la solicitud de Reinversión, tu dinero se reinvertirá al
                                                    finalizar la inversión </p>
                                            </li>
                                        @break

                                        @case(7)
                                            <li class="list-group-item text-primary">
                                                <p> Se recibió la solicitud de Reinversión, tu dinero se reinvertirá al
                                                    finalizar la inversión </p>
                                                <br>
                                                <p> recibirás tu dinero excedente 30 dias habiles después de finalizar la
                                                    inversión </p>
                                            </li>
                                        @break

                                        @default
                                            <li class="list-group-item text-primary">
                                                <p> Se recibió el comprobante </p>
                                            </li>
                                    @endswitch
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            @empty
                <div class="container" style="min-height: 40vh;">
                    <div class="card bg-dark text-white">
                        <img src="https://images.unsplash.com/photo-1593642532400-2682810df593?ixid=MXwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                            class="card-img"
                            alt="https://images.unsplash.com/photo-1593642532400-2682810df593?ixid=MXwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                            style="opacity: 0.4; ">
                        <div class="card-img-overlay d-flex flex-column justify-content-center align-items-center">
                            <h5 class="card-title">Aún no has invertido.</h5>
                            <p class="card-text">¡Invierte aquí para comenzar a ganar!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $inversionesActivas->links() }}
            </div>
        </div>
    </div>
