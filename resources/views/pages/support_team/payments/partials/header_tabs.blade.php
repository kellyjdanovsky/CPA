<div class="card mb-4 border-0 shadow-sm" style="background: transparent;">
    <div class="card-body p-0">
        <ul class="nav nav-pills nav-fill payment-nav p-2 bg-white rounded-lg shadow-sm">
            <li class="nav-item">
                <a href="{{ route('payments.index') }}" 
                   class="nav-link {{ Route::is('payments.index') || Route::is('payments.filter') ? 'active' : '' }}">
                    <i class="icon-cash3 mr-2"></i>
                    <span class="font-weight-bold">Encaissements</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('payments.decaissements.index') }}" 
                   class="nav-link {{ Route::is('payments.decaissements*') ? 'active' : '' }}">
                    <i class="icon-file-text2 mr-2"></i>
                    <span class="font-weight-bold">Décaissements</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('payments.recettes.index') }}" 
                   class="nav-link {{ Route::is('payments.recettes*') ? 'active' : '' }}">
                    <i class="icon-coins mr-2"></i>
                    <span class="font-weight-bold">Recettes</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
.payment-nav .nav-link {
    transition: all 0.3s ease;
    border-radius: 8px;
    padding: 12px 20px;
    color: #4a5568;
    margin: 0 5px;
}

.payment-nav .nav-link:hover {
    background-color: #f7fafc;
    color: #2d3748;
    transform: translateY(-1px);
}

.payment-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.payment-nav .nav-link i {
    font-size: 1.1em;
}
</style>
