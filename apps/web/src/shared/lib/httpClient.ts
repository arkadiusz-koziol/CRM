export async function http<T>(
  input: RequestInfo, 
  init?: RequestInit & { schema?: (d: unknown) => T }
): Promise<T> {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 15_000);
  
  try {
    const res = await fetch(input, { 
      ...init, 
      signal: controller.signal,
      credentials: 'include',
    });
    
    if (!res.ok) {
      throw await mapHttpError(res);
    }
    
    const data = (await res.json()) as unknown;
    return init?.schema ? init.schema(data) : (data as T);
  } finally {
    clearTimeout(timeout);
  }
}

async function mapHttpError(res: Response) {
  let payload: any;
  try { 
    payload = await res.json(); 
  } catch {}
  
  const msg = payload?.message ?? res.statusText;
  const err = new Error(msg) as Error & { 
    code?: string; 
    status?: number; 
    details?: unknown 
  };
  err.status = res.status; 
  err.details = payload;
  return err;
}
