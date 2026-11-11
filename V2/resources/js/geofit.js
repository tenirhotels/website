// Привязка GPS → пиксели иллюстрации. Карта нарисована, а не снята в масштабе, поэтому одна плоская
// формула даёт ошибку в сотни пикселей между опорными точками. Здесь — «тонкая пластина» (thin-plate spline):
// проходит через опорные точки (с небольшим сглаживанием), между ними плавно подстраивается под искажения рисунка.

const gauss = (A, b) => {
    const n = b.length;
    const m = A.map((r, i) => [...r, b[i]]);
    for (let i = 0; i < n; i++) {
        let p = i;
        for (let r = i + 1; r < n; r++) if (Math.abs(m[r][i]) > Math.abs(m[p][i])) p = r;
        [m[i], m[p]] = [m[p], m[i]];
        if (Math.abs(m[i][i]) < 1e-12) return null;
        for (let r = 0; r < n; r++) {
            if (r === i) continue;
            const f = m[r][i] / m[i][i];
            for (let c = i; c <= n; c++) m[r][c] -= f * m[i][c];
        }
    }
    return m.map((r, i) => r[n] / r[i]);
};

const U = (r2) => (r2 < 1e-12 ? 0 : 0.5 * r2 * Math.log(r2)); // r² ln r

/** points: [[lat, lng, x, y], …] → (lat, lng) => [x, y], либо null, если точек меньше трёх или они на одной линии. */
export function buildProjector(points, smoothing = 0.02) {
    if (!points || points.length < 3) return null;
    const lat0 = points[0][0], lon0 = points[0][1];
    const local = (lat, lon) => [(lon - lon0) * Math.cos((lat0 * Math.PI) / 180) * 111.32, (lat - lat0) * 110.57]; // км
    const pts = points.map((p) => local(p[0], p[1]));
    const n = pts.length;
    const A = [];
    for (let i = 0; i < n; i++) {
        const row = [];
        for (let j = 0; j < n; j++) row.push(U((pts[i][0] - pts[j][0]) ** 2 + (pts[i][1] - pts[j][1]) ** 2) + (i === j ? smoothing : 0));
        row.push(1, pts[i][0], pts[i][1]);
        A.push(row);
    }
    for (let k = 0; k < 3; k++) {
        const row = new Array(n + 3).fill(0);
        for (let j = 0; j < n; j++) row[j] = k === 0 ? 1 : pts[j][k - 1];
        A.push(row);
    }
    const cx = gauss(A, [...points.map((p) => p[2]), 0, 0, 0]);
    const cy = gauss(A, [...points.map((p) => p[3]), 0, 0, 0]);
    if (!cx || !cy) return null;
    const ev = (c, e, nn) => {
        let v = c[n] + c[n + 1] * e + c[n + 2] * nn;
        for (let j = 0; j < n; j++) v += c[j] * U((e - pts[j][0]) ** 2 + (nn - pts[j][1]) ** 2);
        return v;
    };
    const project = (lat, lon) => {
        const [e, nn] = local(lat, lon);
        return [ev(cx, e, nn), ev(cy, e, nn)];
    };
    project.local = local;
    return project;
}
