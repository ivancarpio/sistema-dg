# optimize_productos_modals.py
with open(r'c:\laragon\www\sistema-dg-local\resources\views\productos\index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update edit & delete buttons in the table row
old_buttons = """                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" 
                                    class="btn-action-icon btn-action-edit" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditar{{ $producto->id }}"
                                    title="Editar producto">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action-icon btn-action-delete" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEliminar{{ $producto->id }}"
                                    title="Eliminar producto">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>"""

new_buttons = """                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" 
                                    class="btn-action-icon btn-action-edit btn-abrir-editar-prod" 
                                    data-id="{{ $producto->id }}"
                                    data-nombre="{{ $producto->nombre }}"
                                    data-codigo="{{ $producto->codigo }}"
                                    data-descripcion="{{ $producto->descripcion }}"
                                    data-precio-venta="{{ $producto->precio_venta }}"
                                    data-precio-cliente="{{ $producto->precio_cliente }}"
                                    data-costo="{{ $producto->costo }}"
                                    data-id-categoria="{{ $producto->id_categoria }}"
                                    data-id-marca="{{ $producto->id_marca }}"
                                    data-id-unidad="{{ $producto->id_unidad }}"
                                    data-id-unidad-sec="{{ $producto->id_unidad_secundaria }}"
                                    data-factor-conversion="{{ $producto->factor_conversion ? number_format($producto->factor_conversion, 0, '', '') : '' }}"
                                    data-precio-venta-sec="{{ $producto->precio_venta_secundario }}"
                                    data-precio-cliente-sec="{{ $producto->precio_cliente_secundario }}"
                                    data-stock-minimo="{{ $producto->stock_minimo }}"
                                    title="Editar producto">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action-icon btn-action-delete btn-abrir-eliminar-prod" 
                                    data-id="{{ $producto->id }}"
                                    data-nombre="{{ $producto->nombre }}"
                                    title="Eliminar producto">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>"""

content_norm = content.replace('\r\n', '\n')
old_b_norm = old_buttons.replace('\r\n', '\n')
new_b_norm = new_buttons.replace('\r\n', '\n')

if old_b_norm in content_norm:
    content_norm = content_norm.replace(old_b_norm, new_b_norm, 1)
    print("SUCCESS: Action buttons updated!")
else:
    print("ERROR: Action buttons not found!")

# 2. Replace the 1201 repeated modals with 1 single dynamic modal
import re

pattern = re.compile(r'<!-- MODALES EDITAR Y ELIMINAR \(Por cada producto\) -->\s*@foreach\(\$productos as \$producto\).+?@endforeach', re.DOTALL)

single_modals = """<!-- MODAL EDITAR DINÁMICO (ÚNICO Y ULTRA-LIVIANO) -->
    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form id="formEditarProducto" action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf @method('PUT')
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="fw-bold m-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Nombre del Producto *</label>
                        <input type="text" name="nombre" id="editNombre" class="form-control form-control-lg fw-bold" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Código *</label>
                        <div class="input-group">
                            <input type="text" name="codigo" id="editCodigo" class="form-control fw-bold fs-6 font-monospace" required>
                            <button class="btn btn-outline-primary fw-bold btn-generar-codigo" type="button" 
                                    data-nombre-id="editNombre" 
                                    data-codigo-id="editCodigo" 
                                    id="btnRegenerarCodigoEdit"
                                    title="Regenerar código estándar">
                                <i class="bi bi-magic me-1"></i> Generar
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción</label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">Precio Venta (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-success">Bs.</span>
                                <input type="number" name="precio_venta" id="editPrecioVenta" step="0.01" class="form-control fw-bold text-success fs-5" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Clientes ocasionales.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">
                                Precio Cliente (Bs.)
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.70rem;">Opcional</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold" style="color: #0284c7;">Bs.</span>
                                <input type="number" name="precio_cliente" id="editPrecioCliente" step="0.01" class="form-control fw-bold fs-5" style="color: #0284c7;">
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Ferreterías / regulares.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-bold">Costo (Bs.) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Bs.</span>
                                <input type="number" name="costo" id="editCosto" step="0.01" class="form-control fw-bold fs-5" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">Costo de adquisición.</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Categoría</label>
                            <select name="id_categoria" id="editCategoria" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Marca</label>
                            <select name="id_marca" id="editMarca" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Unidad de Medida (Principal)</label>
                            <select name="id_unidad" id="editUnidad" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- SECCIÓN: UNIDAD SECUNDARIA / EMPAQUE / VENTA POR MAYOR (OPCIONAL) -->
                    <div class="card border border-primary-subtle bg-light bg-opacity-50 rounded-3 mb-3 p-3 shadow-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2.5">
                                    <i class="bi bi-boxes fs-5"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">Segunda Unidad de Medida / Venta por Mayor</span>
                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.68rem;">Opcional</span>
                                    <div class="text-muted" style="font-size: 0.75rem;">Para productos con 2 formas de venta (ej. Pieza y Caja, Pieza y Paquete, etc.)</div>
                                </div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="checkHabilitarSecEdit" onchange="toggleSeccionUnidadSecundaria(this, 'cajaUnidadSecundariaEdit')" style="cursor: pointer; width: 2.2rem; height: 1.2rem;">
                            </div>
                        </div>

                        <div id="cajaUnidadSecundariaEdit" style="display: none;" class="pt-3 mt-2 border-top">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Unidad Secundaria (Empaque / Paquete / Caja)</label>
                                    <select name="id_unidad_secundaria" id="editUnidadSec" class="form-select">
                                        <option value="">Seleccionar unidad (Caja, Paquete, Rollo...)</option>
                                        @foreach($unidades as $unidad)
                                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" style="font-size: 0.72rem;">Ej. Paquete, Caja, Rollo.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Contenido (Unidades Base por Empaque)</label>
                                    <div class="input-group">
                                        <input type="number" name="factor_conversion" id="editFactorConversion" step="1" min="1" class="form-control fw-bold" placeholder="Ej. 50">
                                        <span class="input-group-text bg-white small text-muted">unidades</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">¿Cuántas piezas/unidades base contiene 1 empaque?</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Precio Venta por Empaque (Bs.)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold text-success">Bs.</span>
                                        <input type="number" name="precio_venta_secundario" id="editPrecioVentaSec" step="0.01" min="0" class="form-control fw-bold text-success fs-5" placeholder="0.00">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Precio de venta al público para clientes ocasionales.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">Precio Cliente por Empaque (Bs.)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold" style="color: #0284c7;">Bs.</span>
                                        <input type="number" name="precio_cliente_secundario" id="editPrecioClienteSec" step="0.01" min="0" class="form-control fw-bold fs-5" style="color: #0284c7;" placeholder="0.00">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Precio preferencial para ferreterías / clientes regulares.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-bold">Stock Mínimo (Alerta)</label>
                        <input type="number" name="stock_minimo" id="editStockMinimo" class="form-control fw-bold fs-5">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-5">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ELIMINAR DINÁMICO (ÚNICO Y ULTRA-LIVIANO) -->
    <div class="modal fade" id="modalEliminarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-inline-flex mb-3">
                        <i class="bi bi-trash3 fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Eliminar Producto?</h5>
                    <p class="text-muted small mb-4">Esta acción no se puede deshacer. Se eliminará <strong id="nombreProductoEliminar">este producto</strong> del catálogo.</p>
                    <form id="formEliminarProducto" action="" method="POST" class="d-flex gap-2">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-light border w-50 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger w-50 fw-bold">Sí, Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>"""

if pattern.search(content_norm):
    content_norm = pattern.sub(single_modals, content_norm)
    print("SUCCESS: 1201 duplicate modals replaced by 1 single dynamic modal!")
else:
    print("ERROR: Repeated modals pattern not found!")

# 3. Add JS event listener for opening the dynamic modals
js_listener = """    // Delegación de eventos para Abrir Modal Editar y Eliminar Dinámico
    const modalEditarEl = document.getElementById('modalEditarProducto');
    const modalEditar = modalEditarEl ? new bootstrap.Modal(modalEditarEl) : null;
    const formEditar = document.getElementById('formEditarProducto');

    const modalEliminarEl = document.getElementById('modalEliminarProducto');
    const modalEliminar = modalEliminarEl ? new bootstrap.Modal(modalEliminarEl) : null;
    const formEliminar = document.getElementById('formEliminarProducto');
    const txtEliminarNombre = document.getElementById('nombreProductoEliminar');

    document.addEventListener('click', function(e) {
        // Clic en Editar
        const btnEdit = e.target.closest('.btn-abrir-editar-prod');
        if (btnEdit && formEditar) {
            const id = btnEdit.dataset.id;
            formEditar.action = '/productos/' + id;

            document.getElementById('editNombre').value = btnEdit.dataset.nombre || '';
            document.getElementById('editCodigo').value = btnEdit.dataset.codigo || '';
            document.getElementById('editDescripcion').value = btnEdit.dataset.descripcion || '';
            document.getElementById('editPrecioVenta').value = btnEdit.dataset.precioVenta || '';
            document.getElementById('editPrecioCliente').value = btnEdit.dataset.precioCliente || '';
            document.getElementById('editCosto').value = btnEdit.dataset.costo || '0.00';
            document.getElementById('editCategoria').value = btnEdit.dataset.idCategoria || '';
            document.getElementById('editMarca').value = btnEdit.dataset.idMarca || '';
            document.getElementById('editUnidad').value = btnEdit.dataset.idUnidad || '';
            document.getElementById('editStockMinimo').value = btnEdit.dataset.stockMinimo || '5';

            const btnRegen = document.getElementById('btnRegenerarCodigoEdit');
            if (btnRegen) {
                btnRegen.dataset.productoId = id;
            }

            // Datos de unidad secundaria
            const idSec = btnEdit.dataset.idUnidadSec;
            const pVentaSec = btnEdit.dataset.precioVentaSec;
            const tieneSec = (idSec && idSec !== '' && pVentaSec && parseFloat(pVentaSec) > 0);

            const checkSec = document.getElementById('checkHabilitarSecEdit');
            const cajaSec = document.getElementById('cajaUnidadSecundariaEdit');

            if (checkSec && cajaSec) {
                checkSec.checked = !!tieneSec;
                cajaSec.style.display = tieneSec ? 'block' : 'none';
                document.getElementById('editUnidadSec').value = idSec || '';
                document.getElementById('editFactorConversion').value = btnEdit.dataset.factorConversion || '';
                document.getElementById('editPrecioVentaSec').value = pVentaSec || '';
                document.getElementById('editPrecioClienteSec').value = btnEdit.dataset.precioClienteSec || '';
            }

            if (modalEditar) modalEditar.show();
            return;
        }

        // Clic en Eliminar
        const btnDelete = e.target.closest('.btn-abrir-eliminar-prod');
        if (btnDelete && formEliminar) {
            const id = btnDelete.dataset.id;
            const nombre = btnDelete.dataset.nombre || 'este producto';
            formEliminar.action = '/productos/' + id;
            if (txtEliminarNombre) txtEliminarNombre.textContent = `"${nombre}"`;
            if (modalEliminar) modalEliminar.show();
            return;
        }
    });
"""

target_js = "document.addEventListener('DOMContentLoaded', function() {"
content_norm = content_norm.replace(target_js, target_js + "\n" + js_listener, 1)

with open(r'c:\laragon\www\sistema-dg-local\resources\views\productos\index.blade.php', 'w', encoding='utf-8', newline='\r\n') as f:
    f.write(content_norm)

print("Done optimizing index.blade.php")
