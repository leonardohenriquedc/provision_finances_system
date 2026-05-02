# 💰 Sistema de Provisionamento Financeiro

Um sistema web desenvolvido para **simulação e planejamento financeiro**, permitindo ao usuário projetar dívidas, parcelamentos e impactos de juros ao longo do tempo.

> ⚠️ Este projeto possui caráter **estimativo** e **não realiza transações financeiras reais**.

---

## 📌 Sobre o Projeto

O Sistema de Provisionamento Financeiro foi criado com o objetivo de fornecer uma visão clara de obrigações futuras, permitindo simular:

* Dívidas únicas
* Parcelamentos
* Aplicação de juros simples e compostos
* Projeção temporal de pagamentos

A aplicação é focada em **organização e análise**, não sendo integrada a bancos ou sistemas de pagamento.

---

## 🚀 Funcionalidades

### 📊 Provisionamento

* Criação de provisionamentos financeiros
* Suporte a:

  * Valor base
  * Juros (simples e composto)
  * Base de juros (dia, mês, ano)
  * Quantidade de parcelas
  * Datas de competência e vencimento

---

### 🧾 Parcelas

* Geração automática de parcelas futuras
* Cada parcela possui:

  * Número
  * Valor calculado
  * Data de vencimento
  * Status:

    * `OPEN` (Em aberto)
    * `PAID` (Pago)
    * `LATE` (Atrasado)

---

### 📈 Dashboard

* Visualização em tabela dos provisionamentos
* Indicadores:

  * Total geral
  * Total pago
  * Total pendente
  * Quantidade de registros
* Filtro por mês

---

### 🔍 Detalhamento

* Visualização completa das parcelas de um provisionamento
* Controle individual de status

---

### ✏️ Atualizações

* Edição de provisionamentos com recálculo automático das parcelas
* Atualização de status de parcelas de forma segura

---

### 🗑️ Exclusão

* Remoção de provisionamentos
* Exclusão automática das parcelas relacionadas (cascade)

---

## 🧠 Regras de Negócio

* O valor informado representa **uma parcela**
* Parcelas são **geradas automaticamente**
* Alterações em provisionamentos:

  * Recalculam todas as parcelas
* Status das parcelas é apenas **informativo (não financeiro)**
* O sistema não armazena pagamentos reais

---

## ⚙️ Tecnologias Utilizadas

* **Laravel** (Backend)
* **Blade** (Template Engine)
* **Tailwind CSS** (UI)
* **MySQL / H2** (Banco de Dados)
* **PHP** 8+

---

## 🗄️ Modelagem

### Provision

* description
* base_amount
* interest_rate
* interest_type
* interest_period
* installments
* competence_date
* first_due_date

### ProvisionInstallment

* provision_id
* installment_number
* amount
* due_date
* status

---

## 🔐 Segurança

* Isolamento de dados por usuário (`user_id`)
* Validação de acesso em todas as operações
* Proteção contra acesso indevido a registros

---

## 🧪 Testes

* Testes de criação de provisionamentos
* Validação de regras de cálculo
* Garantia de integridade das parcelas

---

## ⚠️ Limitações Atuais

* Frequência de parcelas atrelada à base de juros
* Não possui:

  * Integração bancária
  * Controle de pagamentos reais
  * Histórico de alterações (audit log)

---

## 🚀 Melhorias Futuras

* Separação entre:

  * Frequência de parcelas
  * Base de juros
* Dashboard com gráficos
* Paginação e filtros avançados
* Soft delete (lixeira)
* Histórico de alterações
* API REST completa
* Integração com frontend SPA (Angular/React)

---

## 📷 Screens (opcional)

> Adicione prints aqui:

* Dashboard
* Formulário de criação
* Lista de parcelas

---

## 📚 Aprendizados

Este projeto foi desenvolvido com foco em:

* Modelagem de dados financeiros
* Aplicação de regras de negócio
* Relacionamentos no Laravel (Eloquent)
* Boas práticas de backend
* Segurança em aplicações multi-usuário

---

## 👨‍💻 Autor

Desenvolvido por **Leonardo**

---

## 📄 Licença

MIT
