/**
 * Generated from docs/openapi/v1.yaml snapshot.
 * Regenerate on API contract changes.
 */

export interface ApiResponse<T> {
    data: T;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: PaginationLinks;
    meta: PaginationMeta;
}

export interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

export interface PaginationMetaLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationMetaLink[];
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}

export interface PublicCampaign {
    id: number;
    name: string;
    slug?: string;
    status: string;
    description?: string | null;
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
    campaign_id: number;
    type: 'daily' | 'final';
    status: 'pending' | 'running' | 'completed' | 'failed';
    started_at: string | null;
    finished_at: string | null;
    winner_count: number;
}

export interface PublicWinner {
    id: number;
    campaign_id: number;
    draw_run_id: number;
    entry_id: number;
    campaign_name?: string | null;
    prize_name?: string | null;
    winner: {
        email: string | null;
        city: string | null;
    };
    published_at: string;
}

export interface PublicStats {
    entries_total: number;
    winners_total: number;
    active_campaigns: number;
}

export type PublicCampaignResponse = ApiResponse<PublicCampaign>;
export type PublicPrizeListResponse = ApiResponse<PublicPrize[]>;
export type PublicDrawRunListResponse = PaginatedResponse<PublicDrawRun>;
export type PublicWinnerListResponse = PaginatedResponse<PublicWinner>;
export type PublicStatsResponse = ApiResponse<PublicStats>;
