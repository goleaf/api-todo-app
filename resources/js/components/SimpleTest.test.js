import { describe, it, expect } from 'vitest';

// Simple test suite
describe('Basic Tests', () => {
  it('adds numbers correctly', () => {
    expect(1 + 2).toBe(3);
  });

  it('concatenates strings', () => {
    expect('hello ' + 'world').toBe('hello world');
  });

  it('tests boolean logic', () => {
    expect(true).toBe(true);
    expect(false).toBe(false);
    expect(!false).toBe(true);
  });

  it('compares arrays', () => {
    expect([1, 2, 3]).toEqual([1, 2, 3]);
  });

  it('compares objects', () => {
    expect({ name: 'test', value: 123 }).toEqual({ name: 'test', value: 123 });
  });
}); 