#!/usr/bin/env python3
"""Atualiza o .env no servidor com as configuracoes do Cloud LLM."""
import os

env_path = os.path.join(os.path.dirname(__file__), '.env')

# Valores devem vir do ambiente ou do .env existente, NUNCA hardcoded
llm_cloud_url = os.environ.get('LLM_CLOUD_URL', 'https://ollama.com/v1')
llm_cloud_model = os.environ.get('LLM_CLOUD_MODEL', 'glm-5')

with open(env_path, 'r') as f:
    lines = f.readlines()

new_lines = []
llm_section_done = False
for line in lines:
    if line.startswith('LLM_PROVIDER='):
        new_lines.append('LLM_PROVIDER=openai\n')
    else:
        new_lines.append(line)
    # Adicionar bloco cloud apos LLM_MODEL
    if line.startswith('LLM_MODEL=') and not llm_section_done:
        new_lines.append('\n')
        new_lines.append('# === Cloud LLM (Ollama Cloud - GLM-5) ===\n')
        new_lines.append(f'LLM_CLOUD_URL={llm_cloud_url}\n')
        new_lines.append('LLM_CLOUD_API_KEY=  # Configure sua API key no arquivo .env\n')
        new_lines.append(f'LLM_CLOUD_MODEL={llm_cloud_model}\n')
        llm_section_done = True

with open(env_path, 'w') as f:
    f.writelines(new_lines)

print("OK - .env atualizado")