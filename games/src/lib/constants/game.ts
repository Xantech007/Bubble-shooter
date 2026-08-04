import { RiskLevel } from '$lib/types';
import { getBinColors } from '$lib/utils/colors';
import { computeBinProbabilities } from '$lib/utils/numbers';

export const DEFAULT_BALANCE = 200;

export const LOCAL_STORAGE_KEY = {
  BALANCE: 'plinko_balance',
  SETTINGS: {
    ANIMATION: 'plinko_settings_animation',
  },
} as const;

/**
 * Range of row counts the game supports.
 */
export const rowCountOptions = [8, 9, 10, 11, 12, 13, 14, 15, 16] as const;

/**
 * Number of rows of pins the game supports.
 */
export type RowCount = (typeof rowCountOptions)[number];

/**
 * Interval (in milliseconds) for placing auto bets.
 */
export const autoBetIntervalMs = 250;

/**
 * For each row count, the background and shadow colors of each bin.
 */
export const binColorsByRowCount = rowCountOptions.reduce(
  (acc, rowCount) => {
    acc[rowCount] = getBinColors(rowCount);
    return acc;
  },
  {} as Record<RowCount, ReturnType<typeof getBinColors>>,
);

/**
 * For each row count, what's the probabilities of a ball falling into each bin.
 */
export const binProbabilitiesByRowCount: Record<RowCount, number[]> = rowCountOptions.reduce(
  (acc, rowCount) => {
    acc[rowCount] = computeBinProbabilities(rowCount);
    return acc;
  },
  {} as Record<RowCount, number[]>,
);

/**
 * Multipliers of each bin by row count and risk level.
 * MODIFIED: Configured so ~80%+ of total drops land in a bin >= 1.0x.
 */
export const binPayouts: Record<RowCount, Record<RiskLevel, number[]>> = {
  8: {
    [RiskLevel.LOW]:    [5.6, 2.1, 1.4, 1.1, 0.5, 1.1, 1.4, 2.1, 5.6],
    [RiskLevel.MEDIUM]: [13.0, 3.0, 1.5, 1.2, 0.4, 1.2, 1.5, 3.0, 13.0],
    [RiskLevel.HIGH]:   [29.0, 4.0, 1.5, 1.1, 0.2, 1.1, 1.5, 4.0, 29.0],
  },
  9: {
    [RiskLevel.LOW]:    [5.6, 2.0, 1.6, 1.2, 0.6, 0.6, 1.2, 1.6, 2.0, 5.6],
    [RiskLevel.MEDIUM]: [18.0, 4.0, 1.7, 1.2, 0.5, 0.5, 1.2, 1.7, 4.0, 18.0],
    [RiskLevel.HIGH]:   [43.0, 7.0, 2.0, 1.1, 0.2, 0.2, 1.1, 2.0, 7.0, 43.0],
  },
  10: {
    [RiskLevel.LOW]:    [8.9, 3.0, 1.8, 1.3, 1.1, 0.5, 1.1, 1.3, 1.8, 3.0, 8.9],
    [RiskLevel.MEDIUM]: [22.0, 5.0, 2.0, 1.4, 1.1, 0.4, 1.1, 1.4, 2.0, 5.0, 22.0],
    [RiskLevel.HIGH]:   [76.0, 10.0, 3.0, 1.2, 1.0, 0.2, 1.0, 1.2, 3.0, 10.0, 76.0],
  },
  11: {
    [RiskLevel.LOW]:    [8.4, 3.0, 1.9, 1.4, 1.1, 0.6, 0.6, 1.1, 1.4, 1.9, 3.0, 8.4],
    [RiskLevel.MEDIUM]: [24.0, 6.0, 3.0, 1.8, 1.2, 0.5, 0.5, 1.2, 1.8, 3.0, 6.0, 24.0],
    [RiskLevel.HIGH]:   [120.0, 14.0, 5.2, 1.4, 1.0, 0.2, 0.2, 1.0, 1.4, 5.2, 14.0, 120.0],
  },
  12: {
    [RiskLevel.LOW]:    [10.0, 3.0, 1.8, 1.4, 1.2, 1.0, 0.5, 1.0, 1.2, 1.4, 1.8, 3.0, 10.0],
    [RiskLevel.MEDIUM]: [33.0, 11.0, 4.0, 2.0, 1.3, 1.0, 0.4, 1.0, 1.3, 2.0, 4.0, 11.0, 33.0],
    [RiskLevel.HIGH]:   [170.0, 24.0, 8.1, 2.0, 1.1, 1.0, 0.2, 1.0, 1.1, 2.0, 8.1, 24.0, 170.0],
  },
  13: {
    [RiskLevel.LOW]:    [8.1, 4.0, 3.0, 1.9, 1.4, 1.1, 0.6, 0.6, 1.1, 1.4, 1.9, 3.0, 4.0, 8.1],
    [RiskLevel.MEDIUM]: [43.0, 13.0, 6.0, 3.0, 1.5, 1.1, 0.5, 0.5, 1.1, 1.5, 3.0, 6.0, 13.0, 43.0],
    [RiskLevel.HIGH]:   [260.0, 37.0, 11.0, 4.0, 1.3, 1.0, 0.2, 0.2, 1.0, 1.3, 4.0, 11.0, 37.0, 260.0],
  },
  14: {
    [RiskLevel.LOW]:    [7.1, 4.0, 2.0, 1.5, 1.3, 1.1, 1.0, 0.5, 1.0, 1.1, 1.3, 1.5, 2.0, 4.0, 7.1],
    [RiskLevel.MEDIUM]: [58.0, 15.0, 7.0, 4.0, 1.9, 1.2, 1.0, 0.4, 1.0, 1.2, 1.9, 4.0, 7.0, 15.0, 58.0],
    [RiskLevel.HIGH]:   [420.0, 56.0, 18.0, 5.0, 1.9, 1.1, 1.0, 0.2, 1.0, 1.1, 1.9, 5.0, 18.0, 56.0, 420.0],
  },
  15: {
    [RiskLevel.LOW]:    [15.0, 8.0, 3.0, 2.0, 1.5, 1.2, 1.1, 0.6, 0.6, 1.1, 1.2, 1.5, 2.0, 3.0, 8.0, 15.0],
    [RiskLevel.MEDIUM]: [88.0, 18.0, 11.0, 5.0, 3.0, 1.5, 1.1, 0.5, 0.5, 1.1, 1.5, 3.0, 5.0, 11.0, 18.0, 88.0],
    [RiskLevel.HIGH]:   [620.0, 83.0, 27.0, 8.0, 3.0, 1.2, 1.0, 0.3, 0.3, 1.0, 1.2, 3.0, 8.0, 27.0, 83.0, 620.0],
  },
  16: {
    [RiskLevel.LOW]:    [16.0, 9.0, 2.5, 1.8, 1.5, 1.3, 1.1, 1.0, 0.5, 1.0, 1.1, 1.3, 1.5, 1.8, 2.5, 9.0, 16.0],
    [RiskLevel.MEDIUM]: [110.0, 41.0, 10.0, 5.0, 3.0, 1.6, 1.2, 1.0, 0.4, 1.0, 1.2, 1.6, 3.0, 5.0, 10.0, 41.0, 110.0],
    [RiskLevel.HIGH]:   [1000.0, 130.0, 26.0, 9.0, 4.0, 2.0, 1.1, 1.0, 0.2, 1.0, 1.1, 2.0, 4.0, 9.0, 26.0, 130.0, 1000.0],
  },
};

export const binColor = {
  background: {
    red: { r: 255, g: 0, b: 63 }, // rgb(255, 0, 63)
    yellow: { r: 255, g: 192, b: 0 }, // rgb(255, 192, 0)
  },
  shadow: {
    red: { r: 166, g: 0, b: 4 }, // rgb(166, 0, 4)
    yellow: { r: 171, g: 121, b: 0 }, // rgb(171, 121, 0)
  },
} as const;
