                            <div class="client-info">
                                <?php
                                $clienteValido = $cliente && !empty($cliente->nomeCliente)
                                    && strpos($cliente->nomeCliente, 'não encontrado') === false
                                    && strpos($cliente->nomeCliente, 'não vinculado') === false;
                                ?>

                                <h4>
                                    <?php if ($clienteValido): ?>
                                        <?php echo htmlspecialchars($cliente->nomeCliente, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                    <?php else: ?>
                                        <span style="color: #dc3545;"><i class="bx bx-error-circle"></i> Cliente não encontrado</span>
                                    <?php endif; ?>
                                </h4>

                                <?php if ($clienteValido): ?>
                                    <!-- Endereço -->
                                    <div class="client-meta">
                                        <span class="meta-item">
                                            <i class="bx bx-map"></i>
                                            <?php echo htmlspecialchars($cliente->endereco ?: 'Endereço não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                        </span>
                                    </div>

                                    <?php
                                    // Prepara contatos
                                    $contatos = [];
                                    if (!empty($cliente->telefone) && $cliente->telefone !== '-') {
                                        $contatos[] = '<i class="bx bx-phone"></i> ' . htmlspecialchars($cliente->telefone, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                    }
                                    if (!empty($cliente->celular) && $cliente->celular !== '-') {
                                        $contatos[] = '<i class="bx bx-mobile"></i> ' . htmlspecialchars($cliente->celular, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                    }
                                    if (!empty($cliente->email)) {
                                        $contatos[] = '<i class="bx bx-envelope"></i> ' . htmlspecialchars($cliente->email, ENT_COMPAT | ENT_HTML5, 'UTF-8');
                                    }

                                    if (!empty($contatos)):
                                    ?>
                                        <div class="client-meta">
                                            <span class="meta-item">
                                                <?php echo implode(' <span style="margin: 0 8px; color: #ccc;">|</span> ', $contatos); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Documento -->
                                    <?php if (!empty($cliente->documento)): ?>
                                        <div class="client-meta">
                                            <span class="meta-item">
                                                <i class="bx bx-id-card"></i> CPF/CNPJ: <?php echo htmlspecialchars($cliente->documento, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <div class="client-meta" style="color: #856404; background: #fff3cd; padding: 10px; border-radius: 6px; margin-top: 10px;">
                                        <span class="meta-item">
                                            <i class="bx bx-info-circle"></i> Não foi possível carregar os dados do cliente. Verifique se o cliente está vinculado corretamente à OS.
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
