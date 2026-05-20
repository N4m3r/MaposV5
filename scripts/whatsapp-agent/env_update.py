#!/usr/bin/env python3
"""Atualiza o .env no servidor com as configuracoes do Cloud LLM."""
import os

env_path = os.path.join(os.path.dirname(__file__), '.env')

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
        new_lines.append('LLM_CLOUD_URL=https://ollama.com/v1\n')
        new_lines.append('LLM_CLOUD_API_KEY=f6e136e710bb460fa8f20d408d3761d8.64JLJDts4v5fIYV111gLvtqp\n')
        new_lines.append('LLM_CLOUD_MODEL=glm-5\n')
        llm_section_done = True

with open(env_path, 'w') as f:
    f.writelines(new_lines)

print("OK - .env atualizado")