import json, re

with open("/tmp/ex1063_host.json", "r", encoding="utf-8") as f:
    raw = f.read()

items = {}
for m in re.finditer(r'"(\d+)"\s*:\s*(\{.*?\}|\[.*?\]|".*?")', raw, re.DOTALL):
    idx, val = m.group(1), m.group(2)
    try:
        items[idx] = json.loads(val)
    except:
        pass

def resolve(obj, seen=None):
    if seen is None:
        seen = set()
    if isinstance(obj, str) and obj.isdigit() and obj not in seen:
        seen.add(obj)
        return resolve(items.get(obj, obj), seen)
    if isinstance(obj, dict):
        return {k: resolve(v, seen.copy()) for k, v in obj.items()}
    if isinstance(obj, list):
        return [resolve(v, seen.copy()) for v in obj]
    return obj

if "2" in items:
    err = resolve(items["2"])
    print(json.dumps(err, indent=2, ensure_ascii=False))
else:
    print("No error object found at index 2")
    print("Available keys:", list(items.keys())[:10])
