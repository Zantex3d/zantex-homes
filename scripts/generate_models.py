#!/usr/bin/env python3
"""Generate Eloquent models for ZANTEX HOMES from migrations"""
import os
from pathlib import Path

# Create a basic model template generator
MODELS_TO_CREATE = {
    'Estate': 'estates',
    'Property': 'properties',
    'Building': 'buildings',
    'Floor': 'floors',
    'Unit': 'units',
    'PropertyOwner': 'property_owners',
    'LandParcel': 'land_parcels',
    'Subdivision': 'subdivisions',
    'Plot': 'plots',
    'PlotAllocation': 'plot_allocations',
    'Party': 'parties',
    'PartyContact': 'party_contacts',
    'PartyDocument': 'party_documents',
    'Lead': 'leads',
    'LeadSource': 'lead_sources',
    'LeadStage': 'lead_stages',
    'Campaign': 'campaigns',
    'Viewing': 'viewings',
    'Offer': 'offers',
    'Reservation': 'reservations',
    'Sale': 'sales',
    'SaleItem': 'sale_items',
    'SaleInstallment': 'sale_installments',
    'CommissionRule': 'commission_rules',
    'AgentCommission': 'agent_commissions',
    'Tenant': 'tenants',
    'Landlord': 'landlords',
    'Guarantor': 'guarantors',
    'Lease': 'leases',
    'LeaseCharge': 'lease_charges',
    'RentSchedule': 'rent_schedules',
    'DepositRecord': 'deposit_records',
    'TenantInspection': 'tenant_inspections',
    'ShortletUnit': 'shortlet_units',
    'AvailabilityBlock': 'availability_blocks',
    'Booking': 'bookings',
    'GuestCheckin': 'guest_checkins',
    'GuestCheckout': 'guest_checkouts',
    'BookingCancellation': 'booking_cancellations',
    'Account': 'accounts',
    'FiscalYear': 'fiscal_years',
    'AccountingPeriod': 'accounting_periods',
    'JournalEntry': 'journal_entries',
    'JournalLine': 'journal_lines',
    'Invoice': 'invoices',
    'InvoiceLine': 'invoice_lines',
    'Payment': 'payments',
    'PaymentAllocation': 'payment_allocations',
    'PaymentEvent': 'payment_events',
    'BankAccount': 'bank_accounts',
    'BankTransaction': 'bank_transactions',
    'BankReconciliation': 'bank_reconciliations',
    'Expense': 'expenses',
    'Budget': 'budgets',
    'BudgetLine': 'budget_lines',
    'CostCentre': 'cost_centres',
    'TaxCode': 'tax_codes',
    'Project': 'projects',
    'ProjectPhase': 'project_phases',
    'ProjectMilestone': 'project_milestones',
    'BOQ': 'boqs',
    'Contractor': 'contractors',
    'ProjectVariation': 'project_variations',
    'SiteDiary': 'site_diaries',
    'Equipment': 'equipment',
    'MaintenanceTicket': 'maintenance_tickets',
    'WorkOrder': 'work_orders',
    'PreventivePlan': 'preventive_plans',
    'Supplier': 'suppliers',
    'PurchaseRequisition': 'purchase_requisitions',
    'PurchaseOrder': 'purchase_orders',
    'GoodsReceipt': 'goods_receipts',
    'Warehouse': 'warehouses',
    'StockItem': 'stock_items',
    'StockMovement': 'stock_movements',
    'Investor': 'investors',
    'InvestmentAgreement': 'investment_agreements',
    'InvestmentCommitment': 'investment_commitments',
    'InvestmentContribution': 'investment_contributions',
    'DistributionRun': 'distribution_runs',
    'DistributionLine': 'distribution_lines',
    'Shareholder': 'shareholders',
    'ShareClass': 'share_classes',
    'Employee': 'employees',
    'Agent': 'agents',
    'AttendanceRecord': 'attendance_records',
    'LeaveType': 'leave_types',
    'LeaveRequest': 'leave_requests',
    'PayrollPeriod': 'payroll_periods',
    'PayrollRun': 'payroll_runs',
    'Payslip': 'payslips',
    'Lender': 'lenders',
    'Loan': 'loans',
    'LoanRepayment': 'loan_repayments',
    'Document': 'documents',
    'DocumentCategory': 'document_categories',
    'ServiceChargeScheme': 'service_charge_schemes',
    'ServiceChargeCategory': 'service_charge_categories',
    'AuditLog': 'audit_logs',
    'ActivityEvent': 'activity_events',
    'File': 'files',
    'FileVersion': 'file_versions',
    'FileLink': 'file_links',
    'NumberingSequence': 'numbering_sequences',
    'ApprovalWorkflow': 'approval_workflows',
    'ApprovalStep': 'approval_steps',
    'ApprovalRequest': 'approval_requests',
    'ApprovalAction': 'approval_actions',
    'NotificationTemplate': 'notification_templates',
    'Notification': 'notifications',
    'NotificationDelivery': 'notification_deliveries',
    'NotificationConsent': 'notification_consents',
    'Listing': 'listings',
    'ListingMedia': 'listing_media',
    'ListingFeature': 'listing_features',
    'ListingAmenity': 'listing_amenities',
    'PublicInquiry': 'public_inquiries',
    'SavedProperty': 'saved_properties',
    'KPIDefinition': 'kpi_definitions',
    'DashboardLayout': 'dashboard_layouts',
    'SavedReport': 'saved_reports',
    'ReportRun': 'report_runs',
    'PropertyType': 'property_types',
    'ExpenseCategory': 'expense_categories',
    'State': 'states',
    'LGA': 'lgas',
    'Currency': 'currencies',
    'ExchangeRate': 'exchange_rates',
}

MODELS_DIR = Path('app/Models')

def generate_basic_model(model_name, table_name):
    """Generate a basic model"""
    
    # Determine fillable (all columns except IDs and timestamps)
    fillable = "'public_id', 'status'"  # Minimal default
    
    # Determine key relationships
    has_company = table_name not in ['property_types', 'expense_categories', 'states', 'lgas', 'currencies', 'exchange_rates']
    
    model_code = f'''<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;

class {model_name} extends Model
{{
    use HasFactory;

    protected $table = '{table_name}';

    protected $fillable = [
        {fillable},
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {{
        return $query->where('status', 'active');
    }}
}}
'''
    return model_code

if __name__ == '__main__':
    MODELS_DIR.mkdir(parents=True, exist_ok=True)
    
    count = 0
    for model_name, table_name in sorted(MODELS_TO_CREATE.items()):
        model_file = MODELS_DIR / f'{model_name}.php'
        if not model_file.exists():
            code = generate_basic_model(model_name, table_name)
            with open(model_file, 'w') as f:
                f.write(code)
            count += 1
            print(f'Created: {model_name}')
    
    print(f'\nTotal models created: {count}')
