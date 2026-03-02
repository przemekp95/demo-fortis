/**
 * Generated from docs/openapi/v1.yaml snapshot.
 * Regenerate on API contract changes.
 */

export interface PublicCampaign {
    id: number;
    name: string;
    slug?: string;
    status: string;
    starts_at: string;
    ends_at: string;
    final_draw_at: string;
}

export interface PublicPrize {
    id: number;
    name: string;
    draw_type: 'daily' | 'final';
    quantity: number;
    value: number;
}

export interface PublicDrawRun {
    id: number;
    type: 'daily' | 'final';
    status: 'pending' | 'running' | 'completed' | 'failed';
    executed_at: string;
}

export interface PublicWinner {
    id: number;
    draw_run_id: number;
    entry_id: number;
    masked_name?: string;
    published_at: string;
}

export interface PublicStats {
    entries_total: number;
    winners_total: number;
    active_campaign: boolean;
}
